'use strict';

const assert = require('assert').strict;
const fs = require('fs');
const vm = require('vm');

const tests = [];

function test(name, callback) {
    tests.push({ name, callback });
}

function setup(registrations, states, respond) {
    const providers = {};
    const calls = [];
    const watchers = {};
    const module = {
        factory(name, provider) { providers[name] = provider(); },
        directive() {},
        controller(name, provider) { providers[name] = provider[provider.length - 1]; }
    };
    let confirm = false;
    const context = {
        angular: { module: () => module },
        MapasCulturais: { baseURL: '/', opinionPublication: { manual: true, published: false } },
        Swal: { fire: () => Promise.resolve({ isConfirmed: confirm }) },
        getOpinionManagementErrorMessage: (payload, fallback) => payload.error || fallback
    };
    vm.runInNewContext(fs.readFileSync('src/protected/application/lib/modules/OpinionManagement/assets/OpinionManagement/js/admin-tab-registrations.js', 'utf8'), context);
    const scope = {
        data: { registrations },
        $watchCollection: (expression, callback) => { watchers.list = callback; },
        $watch: (key, callback) => { watchers[key] = callback; },
        $on: (event, callback) => { watchers[event] = callback; },
        $evalAsync: callback => callback()
    };
    const http = {
        post(url, payload) {
            calls.push({ url, payload });
            if (respond) return respond(url, payload);
            return Promise.resolve({ data: url.endsWith('registrationStates') ? { states } : { success: true } });
        }
    };
    const controller = {};
    providers.OpinionPublicationController.call(controller, scope, http, { all: promises => Promise.all(promises) },
        { attr: () => '10' }, providers.OpinionSelection);
    return { controller, selection: providers.OpinionSelection, watchers, calls, scope, confirm: () => { confirm = true; } };
}

test('select all includes only loaded registrations ready for publication', async () => {
    const app = setup([{ id: 1, status: 1 }, { id: 2, status: 1 }, { id: 3, status: 1 }], {
        1: { ready: true }, 2: { ready: false }, 3: { ready: false, published: true }, 4: { ready: true }
    });
    await app.controller.refresh();
    app.controller.toggleAll();
    assert.deepEqual(Array.from(app.controller.selected(), reg => reg.id), [1]);
    assert.equal(app.controller.allSelected(), true);
    app.controller.toggleAll();
    assert.equal(app.controller.selected().length, 0);
});

test('changing filters clears selected registrations', async () => {
    const app = setup([{ id: 1, status: 1 }], { 1: { ready: true } });
    await app.controller.refresh();
    app.controller.toggleAll();
    app.watchers.registrationsFilters();
    assert.equal(app.controller.selected().length, 0);
    app.controller.toggleAll();
    app.watchers['data.registrationsFilter']();
    assert.equal(app.controller.selected().length, 0);
});

test('cancelling confirmation does not publish', async () => {
    const app = setup([{ id: 1, number: 'on-1', status: 1 }], { 1: { ready: true } });
    await app.controller.refresh();
    app.controller.toggleAll();
    app.controller.publish();
    await new Promise(resolve => setImmediate(resolve));
    assert.equal(app.calls.filter(call => call.url.endsWith('publishSelectedOpinions')).length, 0);
});

test('confirmation publishes only selected ids and clears selection', async () => {
    const app = setup([{ id: 1, status: 1 }, { id: 2, status: 1 }], { 1: { ready: true }, 2: { ready: true } });
    await app.controller.refresh();
    app.selection.checked[2] = true;
    app.confirm();
    app.controller.publish();
    await new Promise(resolve => setImmediate(resolve));
    const published = app.calls.filter(call => call.url.endsWith('publishSelectedOpinions'));
    assert.equal(published.length, 1);
    assert.deepEqual(Array.from(published[0].payload.registrationIds), [2]);
    assert.equal(app.controller.selected().length, 0);
    assert.equal(app.selection.busy, false);
});

test('large lists load state in bounded batches', async () => {
    const app = setup(Array.from({ length: 501 }, (_, index) => ({ id: index + 1, status: 1 })), {});
    await app.controller.refresh();
    assert.deepEqual(app.calls.map(call => call.payload.registrationIds.length), [500, 1]);
});

test('pagination 50 to 100 to 150 fetches only new ids', async () => {
    const registrations = Array.from({ length: 150 }, (_, index) => ({ id: index + 1, status: 1 }));
    const states = Object.fromEntries(registrations.map(reg => [reg.id, { ready: true }]));
    const app = setup(registrations.slice(0, 50), states, (url, payload) => Promise.resolve({ data: {
        states: Object.fromEntries(Array.from(payload.registrationIds, id => [id, states[id]]))
    } }));
    let previous = [];
    for (const length of [50, 100, 150]) {
        app.scope.data.registrations = registrations.slice(0, length);
        const current = app.scope.data.registrations.map(reg => reg.id + ':' + reg.status);
        await app.watchers.list(current, previous);
        previous = current;
    }
    assert.deepEqual(app.calls.map(call => call.payload.registrationIds.length), [50, 50, 50]);
    assert.equal(new Set(app.calls.flatMap(call => Array.from(call.payload.registrationIds))).size, 150);
    assert.equal(Object.keys(app.selection.states).length, 150);
});

test('concurrent pagination does not repeat ids already in flight', async () => {
    const responses = [];
    const app = setup([{ id: 1, status: 1 }], {}, () => new Promise(resolve => responses.push(resolve)));
    const first = app.watchers.list(['1:1'], []);
    app.scope.data.registrations.push({ id: 2, status: 1 });
    const second = app.watchers.list(['1:1', '2:1'], ['1:1']);
    await app.watchers.list(['1:1', '2:1'], ['1:1', '2:1']);
    assert.deepEqual(app.calls.map(call => Array.from(call.payload.registrationIds)), [[1], [2]]);
    responses[1]({ data: { states: { 2: { ready: true } } } });
    await second;
    assert.equal(app.controller.loading, true);
    responses[0]({ data: { states: { 1: { ready: true } } } });
    await first;
    assert.equal(app.controller.loading, false);
    assert.equal(Object.keys(app.selection.states).length, 2);
});

test('filter invalidation ignores responses from the previous context', async () => {
    const responses = [];
    const app = setup([{ id: 1, status: 1 }], {}, () => new Promise(resolve => responses.push(resolve)));
    const oldRequest = app.controller.refresh();
    const newRequest = app.watchers.registrationsFilters({ keyword: 'new' }, { keyword: 'old' });
    responses[1]({ data: { states: { 1: { ready: false, published: true } } } });
    await newRequest;
    responses[0]({ data: { states: { 1: { ready: true } } } });
    await oldRequest;
    assert.equal(app.selection.states[1].published, true);
    assert.equal(app.selection.states[1].ready, false);
});

test('explicit refresh updates cached states and clears selection', async () => {
    let published = false;
    const app = setup([{ id: 1, status: 1 }], {}, () => Promise.resolve({ data: {
        states: { 1: { ready: !published, published } }
    } }));
    await app.controller.refresh();
    app.controller.toggleAll();
    published = true;
    await app.controller.refresh();
    assert.equal(app.selection.states[1].published, true);
    assert.equal(app.controller.selected().length, 0);
    assert.equal(app.calls.length, 2);
});

test('failed state lookup clears stale selection and can be retried', async () => {
    let failed = true;
    const app = setup([{ id: 1, status: 1 }], {}, () => failed
        ? Promise.reject({ data: { error: 'Unavailable' } })
        : Promise.resolve({ data: { states: { 1: { ready: true } } } }));
    app.selection.checked[1] = true;
    await app.controller.refresh();
    assert.equal(app.controller.error, 'Unavailable');
    assert.equal(app.controller.loading, false);
    assert.equal(Object.keys(app.selection.states).length, 0);
    assert.equal(Object.keys(app.selection.checked).length, 0);
    failed = false;
    await app.controller.refresh();
    assert.equal(app.controller.error, '');
    assert.equal(app.selection.states[1].ready, true);
});

test('status changes invalidate cached readiness', async () => {
    const app = setup([{ id: 1, status: 1 }], { 1: { ready: true } });
    await app.watchers.list(['1:1'], []);
    app.scope.data.registrations[0].status = 2;
    await app.watchers.list(['1:2'], ['1:1']);
    assert.equal(app.calls.length, 2);
});

test('publication selection still rejects more than 500 registrations', async () => {
    const registrations = Array.from({ length: 501 }, (_, index) => ({ id: index + 1, status: 1 }));
    const states = Object.fromEntries(registrations.map(reg => [reg.id, { ready: true }]));
    const app = setup(registrations, states);
    await app.controller.refresh();
    app.controller.toggleAll();
    app.confirm();
    app.controller.publish();
    await new Promise(resolve => setImmediate(resolve));
    assert.equal(app.calls.filter(call => call.url.endsWith('publishSelectedOpinions')).length, 0);
    assert.match(app.controller.error, /500/);
});

test('failed batch invalidates other responses from the same refresh', async () => {
    const responses = [];
    const registrations = Array.from({ length: 501 }, (_, index) => ({ id: index + 1, status: 1 }));
    const app = setup(registrations, {}, () => new Promise((resolve, reject) => responses.push({ resolve, reject })));
    const refresh = app.controller.refresh();
    responses[0].reject({ data: { error: 'Unavailable' } });
    await refresh;
    responses[1].resolve({ data: { states: { 501: { ready: true } } } });
    await new Promise(resolve => setImmediate(resolve));
    assert.equal(Object.keys(app.selection.states).length, 0);
    assert.equal(app.controller.error, 'Unavailable');
    assert.equal(app.controller.loading, false);
});

test('destroyed controller ignores pending responses', async () => {
    let respond;
    const app = setup([{ id: 1, status: 1 }], {}, () => new Promise(resolve => { respond = resolve; }));
    const request = app.controller.refresh();
    app.watchers.$destroy();
    respond({ data: { states: { 1: { ready: true } } } });
    await request;
    assert.equal(Object.keys(app.selection.states).length, 0);
    assert.equal(app.controller.loading, false);
});

async function runTests() {
    let passed = 0;
    let failed = 0;

    for (const { name, callback } of tests) {
        try {
            await callback();
            console.log(`PASS: ${name}`);
            passed++;
        } catch (error) {
            console.error(`FAIL: ${name}`);
            console.error(error);
            failed++;
        }
    }

    console.log('\nTest results:');
    console.log(`Total: ${tests.length}`);
    console.log(`Passed: ${passed}`);
    console.log(`Failed: ${failed}`);

    process.exitCode = failed > 0 ? 1 : 0;
}

runTests();

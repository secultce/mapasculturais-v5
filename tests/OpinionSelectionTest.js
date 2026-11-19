'use strict';

const { test } = require('node:test');
const assert = require('node:assert/strict');
const fs = require('node:fs');
const vm = require('node:vm');

function setup(registrations, states) {
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
        $watchCollection: () => {},
        $watch: (key, callback) => { watchers[key] = callback; },
        $on: () => {},
        $evalAsync: callback => callback()
    };
    const http = {
        post(url, payload) {
            calls.push({ url, payload });
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

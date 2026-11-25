(function (angular) {
    'use strict';

    const module = angular.module('entity.module.opportunity');

    module.factory('OpinionSelection', function () {
        return { states: {}, checked: {}, busy: false, config: MapasCulturais.opinionPublication || {} };
    });

    module.directive('opinionRegistration', ['OpinionSelection', function (selection) {
        return {
            restrict: 'E',
            scope: { registration: '=' },
            template: `<div class="opinion-registration">
                <label ng-if="selection.config.manual && !selection.config.published">
                    <input type="checkbox" ng-model="selection.checked[registration.id]"
                        ng-disabled="selection.busy || !selection.states[registration.id].ready"
                        aria-label="Selecionar inscrição {{registration.number}} para publicar pareceres">
                    <span>{{status()}}</span>
                </label>
                <span ng-if="!selection.config.manual || selection.config.published">{{status()}}</span>
                <button type="button" class="btn btn-default" ng-if="registration.status > 0" ng-click="show()">Visualizar pareceres</button>
            </div>`,
            link: function (scope) {
                scope.selection = selection;
                scope.show = function () { showOpinions(scope.registration.id); };
                scope.status = function () {
                    const state = selection.states[scope.registration.id];
                    if (selection.config.published || (state && state.published)) return 'Publicado';
                    if (!state) return 'Verificando pareceres…';
                    return state.ready ? 'Pronto para publicar' : 'Aguardando avaliações';
                };
            }
        };
    }]);

    module.controller('OpinionPublicationController', ['$scope', '$http', '$q', '$element', 'OpinionSelection',
        function ($scope, $http, $q, $element, selection) {
            const vm = this;
            const opportunityId = $element.attr('data-opportunity-id');
            let version = 0;
            let pending = {};
            vm.loading = false;
            vm.busy = false;
            vm.error = '';

            const registrations = () => ($scope.data.registrations || []).filter(reg => reg.status > 0);
            vm.available = () => registrations().filter(reg => selection.states[reg.id] && selection.states[reg.id].ready);
            vm.selected = () => vm.available().filter(reg => selection.checked[reg.id]);
            vm.allSelected = () => vm.available().length > 0 && vm.selected().length === vm.available().length;
            vm.toggleAll = function () {
                const checked = !vm.allSelected();
                vm.available().forEach(reg => { selection.checked[reg.id] = checked; });
            };

            function request(action, ids) {
                return $http.post(MapasCulturais.baseURL + 'opinionManagement/' + action, {
                    id: opportunityId, registrationIds: ids
                }).then(response => {
                    if (!response.data || typeof response.data !== 'object' || response.data.error) {
                        throw new Error('Não foi possível processar os pareceres. Atualize a página e tente novamente.');
                    }
                    return response.data;
                });
            }

            function errorMessage(error) {
                return getOpinionManagementErrorMessage(error.data || error.message, 'Não foi possível processar os pareceres. Tente novamente.');
            }

            function invalidate() {
                version++;
                pending = {};
                selection.states = {};
                selection.checked = {};
                vm.loading = false;
            }

            function loadStates() {
                const currentVersion = version;
                const ids = Array.from(new Set(registrations().map(reg => reg.id)))
                    .filter(id => !selection.states[id] && !pending[id]);
                const requests = [];
                vm.error = '';
                for (let offset = 0; offset < ids.length; offset += 500) {
                    const batch = ids.slice(offset, offset + 500);
                    batch.forEach(id => { pending[id] = true; });
                    requests.push(request('registrationStates', batch).then(result => {
                        if (currentVersion !== version) return;
                        Object.assign(selection.states, result.states);
                        Object.keys(selection.checked).forEach(id => {
                            if (!selection.states[id] || !selection.states[id].ready) delete selection.checked[id];
                        });
                    }).finally(() => {
                        if (currentVersion !== version) return;
                        batch.forEach(id => { delete pending[id]; });
                        vm.loading = Object.keys(pending).length > 0;
                    }));
                }
                vm.loading = Object.keys(pending).length > 0;
                return $q.all(requests).catch(error => {
                    if (currentVersion !== version) return;
                    invalidate();
                    vm.error = errorMessage(error);
                });
            }

            vm.refresh = function () {
                invalidate();
                return loadStates();
            };

            vm.publish = function () {
                const selected = vm.selected().slice();
                if (vm.busy || !selected.length) return;
                if (selected.length > 500) {
                    vm.error = 'Selecione no máximo 500 inscrições por publicação.';
                    return;
                }
                // Plain text prevents registration numbers from being interpreted as HTML.
                Swal.fire({
                    title: 'Publicar pareceres de ' + selected.length + ' inscrição(ões)?',
                    text: 'Inscrições: ' + selected.map(reg => reg.number).join(', ') + '. Os proponentes poderão consultar os pareceres e serão notificados. Esta ação não pode ser desfeita.',
                    icon: 'warning', showCancelButton: true,
                    confirmButtonText: 'Publicar pareceres', cancelButtonText: 'Cancelar'
                }).then(result => {
                    if (!result.isConfirmed) return;
                    $scope.$evalAsync(function () {
                        vm.busy = selection.busy = true;
                        request('publishSelectedOpinions', selected.map(reg => reg.id)).then(() => {
                            selection.checked = {};
                            return vm.refresh().then(() => Swal.fire({
                                title: 'Pareceres publicados', text: 'Os pareceres das inscrições selecionadas estão disponíveis aos proponentes.', icon: 'success'
                            }));
                        }).catch(error => {
                            vm.error = errorMessage(error);
                            Swal.fire({ title: 'Não foi possível publicar os pareceres', text: vm.error, icon: 'error' });
                        }).finally(() => { vm.busy = selection.busy = false; });
                    });
                });
            };

            $scope.$watchCollection(() => registrations().map(reg => reg.id + ':' + reg.status), (current, previous) => {
                const loaded = new Set(current);
                if (previous && previous.some(value => !loaded.has(value))) return vm.refresh();
                return loadStates();
            });
            function filtersChanged(current, previous) {
                selection.checked = {};
                if (current !== previous) return vm.refresh();
            }
            $scope.$watch('registrationsFilters', filtersChanged, true);
            $scope.$watch('data.registrationsFilter', filtersChanged);
            $scope.$on('$destroy', invalidate);
        }
    ]);
})(angular);

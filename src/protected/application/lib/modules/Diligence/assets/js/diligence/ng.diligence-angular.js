(function (angular) {
    var module = angular.module('DiligenceAngular', ['ngSanitize']);

    // modifica as requisições POST para que sejam lidas corretamente pelo Slim
    module.config(['$httpProvider', function ($httpProvider) {
        $httpProvider.defaults.headers.post['Content-Type'] = 'application/x-www-form-urlencoded;charset=utf-8';
        $httpProvider.defaults.headers.put['Content-Type'] = 'application/x-www-form-urlencoded;charset=utf-8';
        $httpProvider.defaults.headers.common['X_REQUESTED_WITH'] = 'XMLHttpRequest';
        $httpProvider.defaults.transformRequest = function (data) {
            var result = angular.isObject(data) && String(data) !== '[object File]' ? $.param(data) : data;

            return result;
        };
    }]);

    // Seriço que executa no servidor as requisições HTTP
    module.factory('ItemService', ['$http', function ($http) {
        return {};
    }]);

    // Controlador da interface
    module.controller('ItemController', ['$scope', 'ItemService', function ($scope, ItemService) {
        $scope.data = {
            items: [
              {id: 1, title: 'Título 1'},
              {id: 2, title: 'Título 2'}
            ]
          };
    }]);
})(angular);
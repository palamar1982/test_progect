angular.module('app', ['ui.router', 'angular-loading-bar', 'ui.bootstrap', 'ui-notification', 'ui.grid', 'ui.grid.pagination']);

(function () {
    'use strict';

    angular
        .module('app')
        .config(['$stateProvider', '$urlRouterProvider', appConfig]);

    function appConfig($stateProvider, $urlRouterProvider, $locationProvider) {
        $urlRouterProvider.otherwise(function ($injector) {
            var $state = $injector.get('$state');
            $state.go('login', {});
        });
        $stateProvider
            .state('login', {
                url: '/login', templateUrl: 'wwwroot/views/login.html', controller: 'loginController', controllerAs: 'login'
            })
            .state('register', {
                url: '/register', templateUrl: 'wwwroot/views/register.html', controller: 'registerController', controllerAs: 'register'
            })
            .state('logout', {
                controller: function ($injector) {
                    var userService = $injector.get('userService');
                    userService.logout();
                }
            })
            .state('email', {
                abstract: true,
                url: '/email',
                views: {
                    '': { templateUrl: 'wwwroot/views/email.html', controller: 'emailController', controllerAs: 'email' }
                }
            })
            .state('email.new', {
                url: '/email/new',
                views: {
                     '': { templateUrl: 'wwwroot/views/email-new.html' },
                    'composer@email.new': { templateUrl: 'wwwroot/views/email-form.html', controller: 'emailComposerController', controllerAs: 'composer' },
                    'results@email.new': { templateUrl: 'wwwroot/views/email-results.html', controller: 'emailResultsController', controllerAs: 'results' }
                }
            })
            .state('email.history', {
                url: '/email/history',
                templateUrl: 'wwwroot/views/email-history.html',
                controller: 'emailHistoryController', 
                controllerAs: 'history'
            });
    };
})();

(function () {
    'use strict';

    angular
        .module('app')
        .config(['$httpProvider', 'NotificationProvider', '$locationProvider',appConfig]);

    function appConfig($httpProvider, notificationProvider, $locationProvider) {
        $httpProvider.defaults.useXDomain = true;
        $httpProvider.defaults.headers.post["Content-Type"] = "application/json";
        delete $httpProvider.defaults.headers.common['X-Requested-With'];
        $locationProvider.html5Mode(true);
        notificationProvider.setOptions({
            delay: 10000,
            startTop: 20,
            startRight: 10,
            verticalSpacing: 20,
            horizontalSpacing: 20,
            positionX: 'right',
            positionY: 'top'
        });
    };

})();
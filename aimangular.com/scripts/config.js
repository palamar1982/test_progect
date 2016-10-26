(function () {
    'use strict';

    angular.module('app')
        .constant('API', {
            base: 'http://aimsa.com',

            login: '/app_dev.php/api/login',
            logout: '/app_dev.php/logout',
            register: '/app_dev.php/api/register',
            token: '/app_dev.php/api/token',
            email: '/app_dev.php/api/letters/add',
            history: '/app_dev.php/api/letters/all'
        });

})();
(function () {
    'use strict';

    angular
        .module('app')
        .factory('userService', userService);

    userService.$inject = ['$http', 'API', '$state', 'Notification'];

    function userService($http, apiUrl, $state, notification) {
        var user = { token: null, name: null, email: null };

        var service = {
            login: login,
            logout: logout,
            register: register,
            getAntiforgeryToken: antiforgery,
            user: getUserInfo
        };

        return service;

        function getUserInfo() {
            return user;
        }

        function login(form, callback) {
            return $http({
                url: apiUrl.base + apiUrl.login,
                dataType: 'json',
                method: 'POST',
                data: form
            }).success(function (response) {
                if (response.code == 0) {
                    user.token = response.token;
                    user.name = response.name;
                    user.email = response.email;

                    callback(response);
                }
                else {
                    var msg = response.message || 'Error has occured';
                    notification.error(msg);
                }
            }).error(function (error) {
                console.error('http error: ', error);
            });
        }

        function logout() {
            console.log('logging out...');
            user = { token: null, name: null, email: null };
            $state.go('login');
        }

        function antiforgery(callback) {
            return $http({
                url: apiUrl.base + apiUrl.token,
                    dataType: 'json',
                    method: 'GET'
                }).success(function (response) {
                if (response.code == 0) {
                    callback(response);
                }
                else {
                    var msg = response.message || 'Error has occured';
                    notification.error(msg);
                }
            }).error(function (error) {
                console.error('http error: ', error);
            });
        }

        function register(form, callback) {
            return $http({
                url: apiUrl.base + apiUrl.register,
                dataType: 'json',
                method: 'POST',
                data: form
            }).success(function (response) {
                if (response.code == 0) {
                    callback(response);
                }
                else {
                    var msg = response.message || 'Error has occured';
                    notification.error(msg);
                }
            }).error(function (error) {
                console.error('http error: ', error);
            });
        }
    };
})();
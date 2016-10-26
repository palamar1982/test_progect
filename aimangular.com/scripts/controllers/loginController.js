(function () {
    'use strict';

    angular
        .module('app')
        .controller('loginController', loginController);

    loginController.$inject = ['$scope', '$state', 'userService'];

    function loginController($scope, $state, service) {
        var vm = this;

        vm.form = { username: '', password: '' }

        vm.tryLogin = function (form) {
            service.login(form, function (response) {
                console.log(response);
                $state.go('email.new');
            });
        }

    };
})();
(function () {
    'use strict';

    angular
        .module('app')
        .controller('registerController', registerController);

    registerController.$inject = ['$scope', 'userService', '$state', 'Notification'];

    function registerController($scope, service, $state, notification) {
        var vm = this;

        vm.form = { username: '', email: '', password: '', repeatPassword: '', token: '' }

        service.getAntiforgeryToken(function (response){
            vm.form.token = response.token;
        })

        vm.samePasswords = function (form) {
            return vm.form.password != vm.form.repeatPassword;
        }

        vm.tryRegister = function (form) {
            service.register(form, function (response) {
                $state.go('login');
                notification.success('Account has been successfuly created. To log in please check your inbox for email confirmation.');
            });
        }
    };
})();
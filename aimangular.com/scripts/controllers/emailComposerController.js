(function () {
    'use strict';

    angular
        .module('app')
        .controller('emailComposerController', emailComposerController);

    emailComposerController.$inject = ['$scope', 'emailService'];

    function emailComposerController($scope, service) {
        var vm = this;

        vm.form = {
            from: $scope.$parent.email.username,
            to: '',
            subject: '',
            message: '',
        };

        vm.validation = {regex: /\S+@\S+\.\S+/}

        vm.validateEmails = function() {
            var text = vm.form.to;

            var addresses = service.split(text);

            var valid = true;
            for (var i = 0; i < addresses.length; i++){
                if (!service.validate(addresses[i])){
                    valid = false;
                }
            }

            return valid;
        }

        vm.send = function (form) {
            $scope.$emit('send-email', form);
        }

        vm.onchange = function(obj){
            vm.form.message = obj;
        }
    };
})();
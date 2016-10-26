(function () {
    'use strict';

    angular
        .module('app')
        .controller('emailController', emailController);

    emailController.$inject = ['$scope', 'emailService', 'userService'];

    function emailController($scope, service, user) {
        if (user.user().token == null){
            user.logout();
        }

        var vm = this;
        vm.username = user.user().email;

        vm.logout = function () {
            user.logout();
        }

        $scope.$on('send-email', function (event, args) {
            service.send(args, function (response) {
                $scope.$broadcast('update-results', response.emails)
            });
        });
    };
})();

(function () {
    'use strict';

    angular
        .module('app')
        .controller('emailHistoryController', emailHistoryController);

    emailHistoryController.$inject = ['$scope', 'emailService', '$modal'];

    function emailHistoryController($scope, service, $modal) {

        var vm = this;
        loadHistory();

        vm.historyGrid = {
            enableSorting: true,
            enableColumnMenus: false,
            paginationPageSizes: [50, 100],
            paginationPageSize: 50,
            data: [],
            columnDefs: [
                { displayName: 'Recepient', field: 'destination', width: '20%' },
                { displayName: 'Subject', field: 'subject', width: '30%' },
                {
                    displayName: 'Message', field: 'message', width: '*',
                    cellClass: 'grid-align ui-grid-cell-contents',
                    cellTemplate: '<div ng-click="grid.appScope.viewMessage(row.entity)" style="cursor: pointer">< click to see message ></div>'
                },
            ],
            appScopeProvider: {
                viewMessage: function (mail) {
                    openMessagePopup(mail);
                }
            }
        };

        function loadHistory() {
            service.history(function(response){
                vm.historyGrid.data = JSON.parse(response.emails);
            })
        }

        function openMessagePopup(mail) {
            var modal = $modal.open({
                animation: true,
                templateUrl: 'wwwroot/views/email-modal.html',
                controller: 'emailModalController',
                controllerAs: 'modal',
                size: 'lg',
                resolve: {
                    mail: function () {
                        return mail;
                    }
                }
            });
        }
}})();
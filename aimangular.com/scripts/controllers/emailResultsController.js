(function () {
    'use strict';

    angular
        .module('app')
        .controller('emailResultsController', emailResultsController);

    emailResultsController.$inject = ['$scope', '$uibModal'];

    function emailResultsController($scope, $modal) {
        var vm = this;

        vm.historyGrid = {
            enableSorting: true,
            enableColumnMenus: false,
            paginationPageSizes: [10, 25, 50],
            paginationPageSize: 10,
            data: [],
            columnDefs: [
                { displayName: 'Recepient', field: 'to', width: '20%' },
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

        $scope.$on('update-results', function (event, array) {
            var data = array;

            updateGrid(data);

            console.log('resulted array: ', vm.historyGrid.data);
        });

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


        function updateGrid(data) {
            var tempArray = vm.historyGrid.data;

            for (var i = 0; i < data.length; i++) {
                console.log(data[i]);
                tempArray.unshift(data[i]);
            }

            vm.historyGrid.data = tempArray;
        }
    };
})();

(function () {
    'use strict';

    angular
        .module('app')
        .controller('emailModalController', emailModalController);

    emailModalController.$inject = ['$scope', '$modalInstance', 'mail'];

    function emailModalController($scope, $instance, mail) {
        var vm = this;
        vm.email = mail;

        vm.close = function() {
            $instance.close();
        }
    };
})();
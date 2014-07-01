/**
 * Created by Michael on 30/06/14.
 */

angular.module("socialDemo")

.controller("AlbumController", function($rootScope, $scope, $location, album) {
        $scope.album = album;

        $rootScope.metadata = album;
        $rootScope.metadata.image = getAbsoluteImageUrl();
        $rootScope.metadata.url = $location.absUrl();

        function getAbsoluteImageUrl() {
            var root = $location.absUrl().split(/album\/\d/)[0];
            return root + album.image;
        }
    })
;
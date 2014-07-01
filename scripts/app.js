/**
 * Created by Michael on 30/06/14.
 */

angular.module("socialDemo", [ 'ngRoute'])

    .config(function($routeProvider, $locationProvider) {
        $routeProvider
            .when('/album/:id', {
                templateUrl: 'partials/album.tpl.html',
                controller: 'AlbumController',
                resolve: {
                    // get the album info by a call to our fake API (just a bunch of static json files)
                    album: function($http, $route) {
                        var promise = $http
                            .get('api/' + $route.current.params.id + '.json')
                            .then(function(response) {
                                return response.data;
                            });
                        return promise;
                    }
                }
            })
            .when('/home', {
                templateUrl: 'partials/home.tpl.html'
            })
            .otherwise({ redirectTo: '/home' });

        // enable HTML5 mode as hashbang-type URLs will not work with mod_rewrite redirection
        $locationProvider.html5Mode(true).hashPrefix('!');
    })

    .controller("AppController", function($rootScope) {

    })
;
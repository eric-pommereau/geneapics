var app = angular.module("app", ['ngRoute']);

app.config(['$routeProvider',
    function($routeProvider) {
        $routeProvider.when('/images', {
            templateUrl : 'partials/list-images.html',
            controller : 'listImagesController'
        }).when('/image/:idImage', {
            templateUrl : 'partials/view-image.html',
            controller : 'viewImageController'
        }).otherwise({
            redirectTo : '/images'
        });
    }
]);

app.controller('listImagesController', function($scope, $http) {
    $scope.message = "Liste des images";
    var url = "api.php/images";
    $http.get(url).success(function(response) {
        console.log(response);
        $scope.images = response;
    });
    $scope.orderProp = 'ID';
});


app.controller('viewImageController', function($scope, $routeParams, $http) {
    $scope.message = "Visualiser une image";

    var url = "./datas/image.json";
    $scope.master = {};
    $scope.files = [];
    
    $http.get(url).success(function(response) {
        // console.log(response);
        $scope.image = response;
        $scope.master = angular.copy(response);
    });

    $scope.update = function(image) {
        // $scope.master = angular.copy(image);
        console.log("Mise à jour");
        $scope.count++;
    };
    
    $scope.reset = function(image) {
        console.log($scope.master);
        $scope.image = angular.copy($scope.master);
    };
    
    $scope.submitForm = function() {
        
        // check to make sure the form is completely valid
        if ($scope.imageForm.$valid) {
            
            $http({
                method: 'POST',
                url: "api.php/image",
                headers: { 'Content-Type': undefined },
                /*
                transformRequest: function (data) {
                    var formData = new FormData();
                    formData.append("image", angular.toJson(data.image));
                    
                    for (var i = 0; i < data.files; i++) {
                        formData.append("file" + i, data.files[i]);
                    }
                    
                    return formData;
                    
                },
                */
                //Create an object that contains the model and files which will be transformed
                // in the above transformRequest method
                data: { model: $scope.image, files: $scope.files }
            }).
            success(function (data, status, headers, config) {
                alert("success!");
            }).
            error(function (data, status, headers, config) {
                alert("failed!");
            });            
                
            /*
            var responsePromise = $http.put(
                "api.php/image", 
                $scope.image
                );
            
            responsePromise.success(function(data, status, headers, config) {
                // console.log(data, status);
            });
            responsePromise.error(function(data, status, headers, config) {
                console.log(data, status);
            }); 
            */
        }

        
    };
    
    $scope.$on("fileSelected", function (event, args) {
        $scope.$apply(function () {            
            //add the file object to the scope's files collection
            $scope.files.push(args.file);
        });
    });    
    
    /*
    var url = "api.php/image/";
    $http.get(url).success(function(response) {
        $scope.client = response;
    });


    $scope.update = function(user) {
        $scope.master = angular.copy(user);
    };

    $scope.reset = function() {
        $scope.user = angular.copy($scope.master);
    };
    
    $scope.reset();

    $scope.submitForm = function() {

        console.log($scope.clientForm);

        // check to make sure the form is completely valid
        if ($scope.clientForm.$valid) {
            alert('our form is amazing');
        }

    };
   */
});

app.directive('fileUpload', function () {
    return {
        scope: true,        //create a new scope
        link: function (scope, el, attrs) {
            el.bind('change', function (event) {
                var files = event.target.files;
                
                //iterate files since 'multiple' may be specified on the element
                for (var i = 0;i<files.length;i++) {
                    //emit event upward
                    scope.$emit("fileSelected", { file: files[i] });
                }                                       
            });
        }
    };
});

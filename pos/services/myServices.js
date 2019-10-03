app.factory('dataFactory', function($http) {
  $http.defaults.headers.post["Content-Type"] = "text/plain";
  var myService = {
    httpRequest: function(url,method,params,dataPost,upload) {
      var passParameters = {};
      passParameters.url = url;

      if (typeof method == 'undefined'){
        passParameters.method = 'GET';
      }else{
        passParameters.method = method;
      }

      if (typeof params != 'undefined'){
        passParameters.params = params;
        passParameters.params = params;
      }

      if (typeof dataPost != 'undefined'){
        passParameters.data = dataPost;
      }

      if (typeof upload != 'undefined'){
         passParameters.upload = upload;
      }
      
      // passParameters.headers = {'Content-Type': 'application/x-www-form-urlencoded'};
      // console.log(passParameters);
      var promise = $http(passParameters).then(function (response) {
        // if(typeof response.data == 'string' && response.data != 1){
        //   if(response.data.substr('loginMark')){
        //       location.reload();
        //       return;
        //   }
        //   // $.gritter.add({
        //   //   title: 'Application',
        //   //   text: response.data
        //   // });
        //   return false;
        // }
        // if(response.data.jsMessage){
        //   // $.gritter.add({
        //   //   title: response.data.jsTitle,
        //   //   text: response.data.jsMessage
        //   // });
        // }
        return response.data;
      },function(error){
        console.log(error);
      });
      return promise;
    }
  };
  return myService;
});

app.factory('headerFooterData', ['$http', '$q', 
 function($http, $q) {
  return {
    getHeaderFooterData: function(type) {

      var deferred = $q.defer();
        $http.get(window.location.protocol + '//' + window.location.host + '/api/login').success(function(data) {
          deferred.resolve(data);
        }).error(function() {
          deferred.reject();
        });
      return deferred.promise;

    }
  };
 }]);

app.factory('headerFooterData2', ['$http', '$q', 
 function($http, $q) {
  return {
    getHeaderFooterData: function(type) {

      var deferred = $q.defer();
        $http.get(window.location.protocol + '//' + window.location.host + '/api/login/auth').success(function(data) {
          deferred.resolve(data);
        }).error(function() {
          deferred.reject();
        });
      return deferred.promise;
      
    }
  };
 }]);

app.factory('uploadFactory', function ($http) {
    console.log($http);
    return function (file, data, callback) {
        $http({
            url: file,
            method: "POST",
            data: data,
            headers: {'Content-Type': undefined}
        }).success(function (response) {
            callback(response);
        });
    };
});


app.factory('fileUpload', ['$http', function ($http) {
  return {
    uploadFileToUrl : function(file, uploadUrl, $forms){
      var fd = new FormData();
      fd.append('file', file);
      fd.append('item_code', $forms.item_code);
      fd.append('item_name', $forms.item_name);
      fd.append('item_desc', $forms.item_desc);
      fd.append('gl_accounts_id', $forms.gl_accounts_id);
      fd.append('unit_of_measurement_id', $forms.unit_of_measurement_id);
      $http.post(uploadUrl, fd, {
          transformRequest: angular.identity,
          headers: {'Content-Type': undefined,'Process-Data': false}
      })
      .success(function(data){
         return data;
      })
      .error(function(data){
         return data;
      });
    }
  };
 }]);

// app.factory('uploadFile', function ($http) {
//     return function (file, data, callback) {
//         $http({
//             url: file,
//             method: "POST",
//             data: data,
//             headers: {'Content-Type': undefined}
//         }).success(function (response) {
//             callback(response);
//         });
//     };
// });

// app.service('uploadFile', ['$http', function ($http) {
//   this.uploadFileToUrl = function(file, uploadUrl, name){
//        var fd = new FormData();
//        fd.append('file', file);
//        fd.append('name', name);
//        $http.post(uploadUrl, fd, {
//            transformRequest: angular.identity,
//            headers: {'Content-Type': undefined,'Process-Data': false}
//        })
//        .success(function(){
//           console.log("Success");
//        })
//        .error(function(){
//           console.log("Success");
//        });
//    }
// }]);


app.factory('authInterceptor', function($location, $q, $window) {
return {
    request: function(config) {
      config.headers = config.headers || {};
      config.headers.Authorization = 'xxxx-xxxx';
      return config;
    }
  };
})
// .config(function($httpProvider) {
//   $httpProvider.interceptors.push('authInterceptor');
// })
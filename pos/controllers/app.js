app.config(function ($httpProvider) {
  // $httpProvider.defaults.headers.common = { 'Access-Control-Allow-Headers': '*' };
  $httpProvider.defaults.headers.post = {};
  $httpProvider.defaults.headers.put = {};
  $httpProvider.defaults.headers.delete = {};
  $httpProvider.defaults.headers.patch = {};
  // $httpProvider.interceptors.push('authInterceptor');
});
// app.config([
//     "$routeProvider",
//     "$httpProvider",
//     function($routeProvider, $httpProvider){
//         $httpProvider.defaults.headers.common['Access-Control-Allow-Headers'] = '*';
//     }
// ]);

app.controller('ManageController', function(dataFactory, headerFooterData, headerFooterData2, $scope, $http,  $location, $routeParams, $window, $rootScope, $localStorage){

  $scope.libraryTemp = {};
  $scope.innerContent = $routeParams.templateContent;
  $scope.profiles = $localStorage.credentials;
  console.log($scope.profiles);

 if($localStorage.loggedin == true) {
    headerFooterData2.getHeaderFooterData().then(function(data) {
      if(data.login === false) {
        $location.path('login').replace();
      } else {
        console.log(data);
        $scope.nav = data.menu;
      }
    });
  } else {
    headerFooterData.getHeaderFooterData().then(function(data) {
      if(data.login === false) {
        $location.path('login').replace();
      } else {
        console.log(data);
        $scope.nav = data.menu;
      }
    });
  }
 
});

app.controller('Login_Controller', function(dataFactory, $scope, $http, $location, $routeParams, $window, headerFooterData, headerFooterData2, $rootScope, $localStorage){

  $scope.itemPerPage = 5
  $scope.pageNumber = 1;
  $scope.libraryTemp = {};
  $scope.datas = {};
  $scope.data = [];
  $scope.innerContent = $routeParams.templateContent;
  $scope.api = window.location.protocol + '//' + window.location.host + '/api';

  $scope.forms = {};
  $scope.resetForm = function() {
      var selectpicker = angular.element('.selectpicker');
      selectpicker.selectpicker('val', '');
      selectpicker.selectpicker('refresh');
      $scope.form = angular.copy($scope.forms);      
  };
  $scope.resetForm();

  if($localStorage.loggedin == true) {
    headerFooterData2.getHeaderFooterData().then(function(data) {
      if(data.login === false) {
        $location.path('login').replace();
      } else {
        console.log(data);
        $location.path('meals').replace();
        $scope.nav = data.menu;
      }
    });
  } else {
    headerFooterData.getHeaderFooterData().then(function(data) {
      if(data.login === false) {
        $location.path('login').replace();
      } else {
        console.log(data);
        $location.path('meals').replace();
        $scope.nav = data.menu;
      }
    });
  }

  $scope.login = function() {
    console.log($scope.api + '/login/check','POST',{},$scope.form);
    dataFactory.httpRequest($scope.api + '/login/check','POST',{},$scope.form).then(function(data) {
        if (data.login === true) {
          $localStorage.credentials = data;
          $localStorage.loggedin = true; 
          $location.path('meals').replace();
        } else {
          $localStorage.loggedin = false; 
          console.log(data);
        }
    });
  }

});

app.controller('Logout_Controller', function(dataFactory, $scope, $http, $location, $routeParams, $window, headerFooterData, headerFooterData2, $rootScope, $localStorage){

  $scope.itemPerPage = 5
  $scope.pageNumber = 1;
  $scope.libraryTemp = {};
  $scope.datas = {};
  $scope.data = [];
  $scope.innerContent = $routeParams.templateContent;
  $scope.api = window.location.protocol + '//' + window.location.host + '/api';

  headerFooterData.getHeaderFooterData().then(function(data) {
    if(data.login === false) {
      $location.path('login').replace();
    } else {
      $location.path('meals').replace();
      $scope.nav = data.menu;
    }
  });

  $scope.logout = function() {
    dataFactory.httpRequest($scope.api + '/login/logout','POST',{},'').then(function(data) {
        $localStorage.loggedin = false;
        localStorage.clear();
        sessionStorage.clear();
        $location.path('login').replace();
    });
  }
  $scope.logout();

});


app.controller('ProductController', function(dataFactory, $scope, $http, $location, $routeParams, $window, headerFooterData, headerFooterData2, $rootScope, $localStorage){

  $scope.itemPerPage = 5
  $scope.pageNumber = 1;
  $scope.libraryTemp = {};
  $scope.datas = {};
  $scope.data = [];
  $scope.profiles = $localStorage.credentials;
  $scope.innerContent = $routeParams.templateContent;
  $scope.api = window.location.protocol + '//' + window.location.host + '/api';

  if($localStorage.loggedin == true) {
    headerFooterData2.getHeaderFooterData().then(function(data) {
      if(data.login === false) {
        $location.path('login').replace();
      } else {
        console.log(data);
        $scope.nav = data.menu;
      }
    });
  } else {
    headerFooterData.getHeaderFooterData().then(function(data) {
      if(data.login === false) {
        $location.path('login').replace();
      } else {
        console.log(data);
        $scope.nav = data.menu;
      }
    });
  }  

  $scope.item = {};
  $scope.resetForm = function() {
      var selectpicker = angular.element('.selectpicker');
      selectpicker.selectpicker('val', '');
      selectpicker.selectpicker('refresh');
      $scope.form = angular.copy($scope.item);
  };
  $scope.resetForm();

  getResultsPage(1);
  function getResultsPage(pageNumber, search) {
    console.log(search);
      if(search != undefined && search != ''){
          dataFactory.httpRequest($scope.api + '/products?search=' + search + '&page=' + pageNumber).then(function(data) {
              $scope.data = data.data;
              $scope.totalItems = data.total;
              $scope.pageNumber = pageNumber;
              console.log($scope.totalItems);
          });
      }else{
          dataFactory.httpRequest($scope.api + '/products?page=' + pageNumber).then(function(data) {
              $scope.data = data.data;
              $scope.totalItems = data.total;
              $scope.pageNumber = pageNumber;
              console.log($scope.totalItems);
          });
      }
  }

  $scope.pageChanged = function(newPage) {
    getResultsPage(newPage, $scope.datas.searchText);
  };

  $scope.searchDB = function(){
    getResultsPage(1, $scope.datas.searchText);
  }

  $scope.remove = function(item, index){
    swal({
        title: 'Are you sure?',
        text: "You won't be able to revert this!",
        type: 'warning',
        showCancelButton: true,
        confirmButtonText: "Yes, remove it!",
    }).then(function(result) {
        if (result.value) {
            dataFactory.httpRequest($scope.api + '/products/remove/' + item + '?user_id=' + $scope.profiles.user_id, 'GET').then(function(data) {
              $scope.data.splice(index,1);
              getResultsPage($scope.pageNumber);
            });
            swal(
                'Deleted!',
                'The product category has been successfully removed.',
                'success'
            );
        }
    });
  }

  $scope.editProductCategory = function(id){
    dataFactory.httpRequest($scope.api + '/products/edit/' + id).then(function(data) {
        console.log(data);
        $scope.form = data;        
    });
  }

  $scope.saveProductCategory = function(){
    console.log($scope.api + '/products/create?user_id=' + $scope.profiles.user_id);
    dataFactory.httpRequest($scope.api + '/products/create?user_id=' + $scope.profiles.user_id,'POST',{},$scope.form).then(function(data) {
      $scope.data.push(data);
      $scope.info = data;
      console.log(data);
      if($scope.info.message) {
        swal(
            $scope.info.header,
            $scope.info.message,
            $scope.info.type
        );
      }
      var modal_element = angular.element('.modal');
      modal_element.modal('hide');
      getResultsPage(1);
    });
  }

  $scope.updateProductCategory = function(){
    dataFactory.httpRequest($scope.api + '/products/update/' + $scope.form.product_category_id + '?user_id=' + $scope.profiles.user_id, 'POST', {}, $scope.form).then(function(data) {
      $scope.data.push(data);
      $scope.info = data;
      console.log(data);
      if($scope.info.message) {
        swal(
            $scope.info.header,
            $scope.info.message,
            $scope.info.type
        );
      }
      var modal_element = angular.element('.modal');
      modal_element.modal('hide');
      getResultsPage(1);
    });
  }

});

app.controller('ArchivedProductController', function(dataFactory, $scope, $http, $location, $routeParams, $window, headerFooterData, headerFooterData2, $rootScope, $localStorage){

  $scope.itemPerPage = 5
  $scope.pageNumber = 1;
  $scope.libraryTemp = {};
  $scope.datas = {};
  $scope.data = [];
  $scope.innerContent = $routeParams.templateContent;
  $scope.api = window.location.protocol + '//' + window.location.host + '/api';

  if($localStorage.loggedin == true) {
    headerFooterData2.getHeaderFooterData().then(function(data) {
      if(data.login === false) {
        $location.path('login').replace();
      } else {
        console.log(data);
        $scope.nav = data.menu;
      }
    });
  } else {
    headerFooterData.getHeaderFooterData().then(function(data) {
      if(data.login === false) {
        $location.path('login').replace();
      } else {
        console.log(data);
        $scope.nav = data.menu;
      }
    });
  }  

  getResultsPage(1);
  function getResultsPage(pageNumber, search) {
    console.log(search);
      if(search != undefined && search != ''){
          dataFactory.httpRequest($scope.api + '/products/archived?search=' + search + '&page=' + pageNumber).then(function(data) {
              $scope.data = data.data;
              $scope.totalItems = data.total;
              $scope.pageNumber = pageNumber;
              console.log($scope.totalItems);
          });
      }else{
          dataFactory.httpRequest($scope.api + '/products/archived?page=' + pageNumber).then(function(data) {
              $scope.data = data.data;
              $scope.totalItems = data.total;
              $scope.pageNumber = pageNumber;
              console.log($scope.totalItems);
          });
      }
  }

  $scope.pageChanged = function(newPage) {
      getResultsPage(newPage, $scope.datas.searchText);
  };

  $scope.searchDB = function(){
      getResultsPage(1, $scope.datas.searchText);
  }

});

app.controller('SuppliersController', function(dataFactory, headerFooterData, headerFooterData2, $scope, $http,  $location, $routeParams, $window, $rootScope, $localStorage){

  $scope.itemPerPage = 5
  $scope.pageNumber = 1;
  $scope.libraryTemp = {};
  $scope.data = [];
  $scope.innerContent = $routeParams.templateContent;  
  $scope.api = window.location.protocol + '//' + window.location.host + '/api';

  if($localStorage.loggedin == true) {
    headerFooterData2.getHeaderFooterData().then(function(data) {
      if(data.login === false) {
        $location.path('login').replace();
      } else {
        console.log(data);
        $scope.nav = data.menu;
      }
    });
  } else {
    headerFooterData.getHeaderFooterData().then(function(data) {
      if(data.login === false) {
        $location.path('login').replace();
      } else {
        console.log(data);
        $scope.nav = data.menu;
      }
    });
  }  

  $scope.payment_terms = [
    { name: 'Payment Terms 1', value: '1' }, 
    { name: 'Payment Terms 2', value: '2' }, 
    { name: 'Payment Terms 3', value: '3' }
  ];
  
  $scope.shipping_method = [
    { name: 'Delivery', value: '1' }, 
    { name: 'Pick-up', value: '2' }, 
    { name: 'Delivery & Pick-up', value: '3' }
  ];

  $scope.suppliers = {};
  $scope.resetForm = function() {
      var selectpicker = angular.element('.selectpicker');
      selectpicker.selectpicker('val', '');
      selectpicker.selectpicker('refresh');
      $scope.form = angular.copy($scope.suppliers);      
  };
  $scope.resetForm();

  angular.element(document).on('hidden.bs.modal', '.modal', function () {
      $scope.resetForm();
  });

  getResultsPage(1);
  function getResultsPage(pageNumber) {
      if(! $.isEmptyObject($scope.libraryTemp)){
          dataFactory.httpRequest($scope.api + '/suppliers?search=' + $scope.searchTextSupplier + '&page=' + pageNumber).then(function(data) {
            $scope.data = data.data;
            $scope.totalItems = data.total;
            $scope.pageNumber = pageNumber;
          });
      }else{
        dataFactory.httpRequest($scope.api + '/suppliers?page=' + pageNumber).then(function(data) {
          $scope.data = data.data;
          $scope.totalItems = data.total;
          $scope.pageNumber = pageNumber;
        });
      }
  }

  $scope.pageChanged = function(newPage) {
    getResultsPage(newPage);
  };

  $scope.searchSupplier = function(){
      if($scope.searchTextSupplier.length >= 3){
          if($.isEmptyObject($scope.libraryTemp)){
              $scope.libraryTemp = $scope.data;
              $scope.totalItemsTemp = $scope.totalItems;
              $scope.data = {};
          }
          getResultsPage(1);
      }else{
          if(! $.isEmptyObject($scope.libraryTemp)){
              $scope.data = $scope.libraryTemp ;
              $scope.totalItems = $scope.totalItemsTemp;
              $scope.libraryTemp = {};
          }
      }
  }

  $scope.remove = function(item,index){
    swal({
        title: 'Are you sure?',
        text: "You won't be able to revert this!",
        type: 'warning',
        showCancelButton: true,
        confirmButtonText: "Yes, remove it!",
    }).then(function(result) {
        if (result.value) {
            dataFactory.httpRequest($scope.api + '/suppliers/delete/' + item, 'DELETE').then(function(data) {
              $scope.data.splice(index, 1);
              getResultsPage($scope.pageNumber);
            });
            swal(
                'Removed!',
                'The supplier has been successfully deleted.',
                'success'
            );
        }
    });
  }

  $scope.edit = function(id){
    dataFactory.httpRequest($scope.api + '/suppliers/edit/' + id).then(function(data) {
        console.log(data);
        $scope.form = data;
        angular.element(document).ready(function () { 
          $('select').selectpicker("render"); 
        });
    });
  }

  $scope.saveSupplier = function(){
    dataFactory.httpRequest($scope.api + '/suppliers/create','POST',{},$scope.form).then(function(data) {
      $scope.data.push(data);
      $scope.info = data;
      console.log(data);
      if($scope.info.message) {
        swal(
            $scope.info.header,
            $scope.info.message,
            $scope.info.type
        );
      }
      var modal_element = angular.element('.modal');
      modal_element.modal('hide');
      getResultsPage(1);
    });
  }

  $scope.updateSupplier = function(){
    console.log('success');
    dataFactory.httpRequest($scope.api + '/suppliers/update/' + $scope.form.suppliers_id , 'POST', {}, $scope.form).then(function(data) {
      $scope.data.push(data);
      $scope.info = data;
      console.log(data);
      if($scope.info.message) {
        swal(
            $scope.info.header,
            $scope.info.message,
            $scope.info.type
        );
      }
      var modal_element = angular.element('.modal');
      modal_element.modal('hide');
      getResultsPage(1);
    });
  }

});

app.controller('ArchivedSuppliersController', function(dataFactory, headerFooterData, headerFooterData2, $scope, $http,  $location, $routeParams, $window, $rootScope, $localStorage){

  $scope.itemPerPage = 5
  $scope.pageNumber = 1;
  $scope.libraryTemp = {};
  $scope.data = [];
  $scope.innerContent = $routeParams.templateContent;  
  $scope.api = window.location.protocol + '//' + window.location.host + '/api';

  if($localStorage.loggedin == true) {
    headerFooterData2.getHeaderFooterData().then(function(data) {
      if(data.login === false) {
        $location.path('login').replace();
      } else {
        console.log(data);
        $scope.nav = data.menu;
      }
    });
  } else {
    headerFooterData.getHeaderFooterData().then(function(data) {
      if(data.login === false) {
        $location.path('login').replace();
      } else {
        console.log(data);
        $scope.nav = data.menu;
      }
    });
  }  

  getResultsPage(1);
  function getResultsPage(pageNumber) {
      if(! $.isEmptyObject($scope.libraryTemp)){
          dataFactory.httpRequest($scope.api + '/suppliers/archived?search=' + $scope.searchTextSupplier + '&page=' + pageNumber).then(function(data) {
            $scope.data = data.data;
            $scope.totalItems = data.total;
            $scope.pageNumber = pageNumber;
          });
      }else{
        dataFactory.httpRequest($scope.api + '/suppliers/archived?page=' + pageNumber).then(function(data) {
          $scope.data = data.data;
          $scope.totalItems = data.total;
          $scope.pageNumber = pageNumber;
        });
      }
  }

  $scope.pageChanged = function(newPage) {
    getResultsPage(newPage);
  };

  $scope.searchSupplier = function(){
      if($scope.searchTextSupplier.length >= 3){
          if($.isEmptyObject($scope.libraryTemp)){
              $scope.libraryTemp = $scope.data;
              $scope.totalItemsTemp = $scope.totalItems;
              $scope.data = {};
          }
          getResultsPage(1);
      }else{
          if(! $.isEmptyObject($scope.libraryTemp)){
              $scope.data = $scope.libraryTemp ;
              $scope.totalItems = $scope.totalItemsTemp;
              $scope.libraryTemp = {};
          }
      }
  }

  $scope.remove = function(item,index){
    swal({
        title: 'Are you sure?',
        text: "You won't be able to revert this!",
        type: 'warning',
        showCancelButton: true,
        confirmButtonText: "Yes, restore it!",
    }).then(function(result) {
        if (result.value) {
            dataFactory.httpRequest($scope.api + '/suppliers/restore/' + item, 'DELETE').then(function(data) {
              $scope.data.splice(index, 1);
              getResultsPage($scope.pageNumber);
            });
            swal(
                'Restored!',
                'The supplier has been successfully restored.',
                'success'
            );
        }
    });
  }

});

app.controller('GL_AccountsController', function(dataFactory, headerFooterData, headerFooterData2, $scope, $http,  $location, $routeParams, $window, $rootScope, $localStorage){

  $scope.itemPerPage = 5
  $scope.pageNumber = 1;
  $scope.libraryTemp = {};
  $scope.data = [];
  $scope.innerContent = $routeParams.templateContent;  
  $scope.api = window.location.protocol + '//' + window.location.host + '/api';

  if($localStorage.loggedin == true) {
    headerFooterData2.getHeaderFooterData().then(function(data) {
      if(data.login === false) {
        $location.path('login').replace();
      } else {
        console.log(data);
        $scope.nav = data.menu;
      }
    });
  } else {
    headerFooterData.getHeaderFooterData().then(function(data) {
      if(data.login === false) {
        $location.path('login').replace();
      } else {
        console.log(data);
        $scope.nav = data.menu;
      }
    });
  }  

  $scope.gl_accounts = {};
  $scope.resetForm = function() {
      var selectpicker = angular.element('.selectpicker');
      selectpicker.selectpicker('val', '');
      selectpicker.selectpicker('refresh');
      $scope.form = angular.copy($scope.gl_accounts);      
  };
  $scope.resetForm();

  angular.element(document).on('hidden.bs.modal', '.modal', function () {
      $scope.resetForm();
  });
  
  getResultsPage(1);
  function getResultsPage(pageNumber) {
      if(! $.isEmptyObject($scope.libraryTemp)){
          dataFactory.httpRequest($scope.api + '/gl-accounts?search=' + $scope.searchTextGL + '&page=' + pageNumber).then(function(data) {
            $scope.data = data.data;
            $scope.totalItems = data.total;
            $scope.pageNumber = pageNumber;
          });
      }else{
        dataFactory.httpRequest($scope.api + '/gl-accounts?page=' + pageNumber).then(function(data) {
          $scope.data = data.data;
          $scope.totalItems = data.total;
          $scope.pageNumber = pageNumber;
        });
      }
  }

  $scope.pageChanged = function(newPage) {
    getResultsPage(newPage);
  };

  $scope.searchSupplier = function(){
      if($scope.searchTextGL.length >= 3){
          if($.isEmptyObject($scope.libraryTemp)){
              $scope.libraryTemp = $scope.data;
              $scope.totalItemsTemp = $scope.totalItems;
              $scope.data = {};
          }
          getResultsPage(1);
      }else{
          if(! $.isEmptyObject($scope.libraryTemp)){
              $scope.data = $scope.libraryTemp ;
              $scope.totalItems = $scope.totalItemsTemp;
              $scope.libraryTemp = {};
          }
      }
  }

  $scope.remove = function(item,index){
    swal({
        title: 'Are you sure?',
        text: "You won't be able to revert this!",
        type: 'warning',
        showCancelButton: true,
        confirmButtonText: "Yes, remove it!",
    }).then(function(result) {
        if (result.value) {
            dataFactory.httpRequest($scope.api + '/gl-  /remove/' + item, 'DELETE').then(function(data) {
              $scope.data.splice(index, 1);
              getResultsPage($scope.pageNumber);
            });
            swal(
                'Removed!',
                'The supplier has been successfully deleted.',
                'success'
            );
        }
    });
  }

  $scope.edit = function(id){
    dataFactory.httpRequest($scope.api + '/gl-accounts/edit/' + id).then(function(data) {
        console.log(data);
        $scope.form = data;
        angular.element(document).ready(function () { 
          $('select').selectpicker("render"); 
        });
    });
  }

  $scope.saveGL = function(){
    dataFactory.httpRequest($scope.api + '/gl-accounts/create','POST',{},$scope.form).then(function(data) {
      $scope.data.push(data);
      $scope.info = data;
      console.log(data);
      if($scope.info.message) {
        swal(
            $scope.info.header,
            $scope.info.message,
            $scope.info.type
        );
      }
      var modal_element = angular.element('.modal');
      modal_element.modal('hide');
      getResultsPage(1);
    });
  }

  $scope.updateGL = function(){
    dataFactory.httpRequest($scope.api + '/gl-accounts/update/' + $scope.form.gl_accounts_id , 'POST', {}, $scope.form).then(function(data) {
      $scope.data.push(data);
      $scope.info = data;
      console.log(data);
      if($scope.info.message) {
        swal(
            $scope.info.header,
            $scope.info.message,
            $scope.info.type
        );
      }
      var modal_element = angular.element('.modal');
      modal_element.modal('hide');
      getResultsPage(1);
    });
  }

});

app.controller('Archived_GL_AccountsController', function(dataFactory, headerFooterData, headerFooterData2, $scope, $http,  $location, $routeParams, $window, $rootScope, $localStorage){

  $scope.itemPerPage = 5
  $scope.pageNumber = 1;
  $scope.libraryTemp = {};
  $scope.data = [];
  $scope.innerContent = $routeParams.templateContent;  
  $scope.api = window.location.protocol + '//' + window.location.host + '/api';

  if($localStorage.loggedin == true) {
    headerFooterData2.getHeaderFooterData().then(function(data) {
      if(data.login === false) {
        $location.path('login').replace();
      } else {
        console.log(data);
        $scope.nav = data.menu;
      }
    });
  } else {
    headerFooterData.getHeaderFooterData().then(function(data) {
      if(data.login === false) {
        $location.path('login').replace();
      } else {
        console.log(data);
        $scope.nav = data.menu;
      }
    });
  }  

  getResultsPage(1);
  function getResultsPage(pageNumber) {
      if(! $.isEmptyObject($scope.libraryTemp)){
          dataFactory.httpRequest($scope.api + '/gl-accounts/archived?search=' + $scope.searchTextGL + '&page=' + pageNumber).then(function(data) {
            $scope.data = data.data;
            $scope.totalItems = data.total;
            $scope.pageNumber = pageNumber;
          });
      }else{
        dataFactory.httpRequest($scope.api + '/gl-accounts/archived?page=' + pageNumber).then(function(data) {
          $scope.data = data.data;
          $scope.totalItems = data.total;
          $scope.pageNumber = pageNumber;
        });
      }
  }

  $scope.pageChanged = function(newPage) {
    getResultsPage(newPage);
  };

  $scope.searchSupplier = function(){
      if($scope.searchTextGL.length >= 3){
          if($.isEmptyObject($scope.libraryTemp)){
              $scope.libraryTemp = $scope.data;
              $scope.totalItemsTemp = $scope.totalItems;
              $scope.data = {};
          }
          getResultsPage(1);
      }else{
          if(! $.isEmptyObject($scope.libraryTemp)){
              $scope.data = $scope.libraryTemp ;
              $scope.totalItems = $scope.totalItemsTemp;
              $scope.libraryTemp = {};
          }
      }
  }

  $scope.restore = function(item,index){
    swal({
        title: 'Are you sure?',
        text: "You won't be able to revert this!",
        type: 'warning',
        showCancelButton: true,
        confirmButtonText: "Yes, restore it!",
    }).then(function(result) {
        if (result.value) {
            dataFactory.httpRequest($scope.api + '/gl-accounts/restore/' + item, 'DELETE').then(function(data) {
              $scope.data.splice(index, 1);
              getResultsPage($scope.pageNumber);
            });
            swal(
                'Restored!',
                'The supplier has been successfully restored.',
                'success'
            );
        }
    });
  }

});

app.controller('Posting_Inventory_Controller', function(dataFactory, headerFooterData, headerFooterData2, fileUpload, $scope, $http, $location, $routeParams, $window, $timeout, $rootScope, $localStorage){

  $scope.segments = window.location.href.substr(window.location.href.lastIndexOf('/') + 1);
  $scope.itemPerPage = 5
  $scope.pageNumber = 1;
  $scope.libraryTemp = {};
  $scope.info = {};
  $scope.data = [];
  $scope.labels = '';
  $scope.innerContent = $routeParams.templateContent;  
  $scope.profiles = $localStorage.credentials;
  $scope.api = window.location.protocol + '//' + window.location.host + '/api';

  $scope.inventory_adjustment = [
    { name: 'Additional Inventory', value: '1' }, 
    { name: 'Deduction Inventory', value: '2' }
  ];

  $scope.item = {};
  $scope.resetForm = function() {
      var selectpicker = angular.element('.selectpicker');
      selectpicker.selectpicker('val', '');
      selectpicker.selectpicker('refresh');
      $scope.form = angular.copy($scope.item);
  };
  $scope.resetForm();

  angular.element(document).on('hidden.bs.modal', '.modal', function () {
      $scope.resetForm();
  });

  if($localStorage.loggedin == true) {
    headerFooterData2.getHeaderFooterData().then(function(data) {
      if(data.login === false) {
        $location.path('login').replace();
      } else {
        console.log(data);
        $scope.nav = data.menu;
      }
    });
  } else {
    headerFooterData.getHeaderFooterData().then(function(data) {
      if(data.login === false) {
        $location.path('login').replace();
      } else {
        console.log(data);
        $scope.nav = data.menu;
      }
    });
  }  

  getResultsPage($scope.pageNumber, '');
  function getResultsPage(pageNumber, search) {
      if(search != undefined && search != ''){
          dataFactory.httpRequest($scope.api + '/products/product-posting?search=' + search + '&page=' + pageNumber).then(function(data) {
            $scope.data = data.data;
            $scope.totalItems = data.total;
            $scope.pageNumber = pageNumber;
          });
      }else{
          dataFactory.httpRequest($scope.api + '/products/product-posting?page=' + pageNumber).then(function(data) {
            $scope.data = data.data;
            $scope.totalItems = data.total;
            $scope.pageNumber = pageNumber;
          });
      }
      console.log($scope.api + '/item/?search=' + search + '&page=' + pageNumber);
  }

  $scope.pageChanged = function(newPage) {
    $scope.pageNumber = newPage;
    getResultsPage($scope.pageNumber, $scope.info.searchText);
  };

  $scope.search = function(){
    getResultsPage(1, $scope.info.searchText);
  }

  $scope.removeItem = function(item, index){
    swal({
        title: 'Are you sure?',
        text: "You won't be able to revert this!",
        type: 'warning',
        showCancelButton: true,
        confirmButtonText: "Yes, remove it!",
    }).then(function(result) {
        if (result.value) {
            dataFactory.httpRequest($scope.api + '/item/remove/' + item, 'POST').then(function(data) {
              $scope.data.splice(index, 1);
              getResultsPage($scope.pageNumber, $scope.info.searchText);
            });
            swal(
                'Removed!',
                'The item has been successfully removed.',
                'success'
            );
        }
    });
  }

  $scope.editItem = function(id){
    dataFactory.httpRequest($scope.api + '/products/product-posting/edit/' + id).then(function(data) {
        console.log(data);
        $scope.form = data;
        angular.element(document).ready(function () { 
            $('select').selectpicker("render"); 
            $timeout(function () {
                angular.element('#edit-item-modal #gl_accounts_id').typeahead('val', data.gl_accounts_id);
            }, 100);
            if(data.item_img != '') {
                angular.element('#edit-item-modal label.custom-file-label').text(data.item_img);
                angular.element('#edit-item-modal label.file-name').text(data.item_img);
            }
        });
        
    });
  }

  $scope.saveItem = function(){
    var files = angular.element('#add-item-modal label.file-name');
    console.log($scope.api + '/item/create?files=' + files.text());
    dataFactory.httpRequest($scope.api + '/item/create?files=' + files.text(),'POST',{}, $scope.form).then(function(data) {
      $scope.data.push(data);
      $scope.info = data;
      console.log(data);
      if($scope.info.message) {
        swal(
            $scope.info.header,
            $scope.info.message,
            $scope.info.type
        );
      }
      var modal_element = angular.element('.modal');
      modal_element.modal('hide');
      getResultsPage(1, $scope.info.searchText);
    });
  }

  $scope.postItem = function(){
    console.log($scope.api + '/products/product-posting/post/' + $scope.form.product_id + '?user_id=' + $scope.profiles.user_id);
    dataFactory.httpRequest($scope.api + '/products/product-posting/post/' + $scope.form.product_id + '?user_id=' + $scope.profiles.user_id,'POST',{}, $scope.form).then(function(data) {
      $scope.data.push(data);
      $scope.info = data;
      console.log(data);
      if($scope.info.message) {
        swal(
            $scope.info.header,
            $scope.info.message,
            $scope.info.type
        );
      }
      var modal_element = angular.element('.modal');
      modal_element.modal('hide');
      getResultsPage($scope.pageNumber, $scope.info.searchText);
    });
  }

  $scope.uploadedFile = function(element) {   
    $scope.$apply(function($scope) {
        $scope.files = element.files;  
    });
    
    var inputLabel = angular.element('label.custom-file-label');
    var inputFiles = angular.element('label.file-name');

    if(element.files.length != 0 ) {
        inputLabel.text(element.files[0].name);
    } else {
        inputLabel.text('Choose File');
    }

    $http({
        url: window.location.protocol + '//' + window.location.host + '/samsv2-api/item/upload',
        method: "POST",
        processData: false,
        headers: { 'Content-Type': undefined },
        data : $scope.formdata,
        transformRequest: function (data) {
            var formData = new FormData();
            var file = $scope.files[0];
            formData.append("file_upload",file); 
            //pass the key name by which we will recive the file
            return formData;  
            },
        }).success(function(data, status, headers, config) {
            $scope.status = data.status;

            if($scope.status == 1)
            {
                $scope.formdata = "";
                $scope.myform.$setPristine();
                //for flush all the validation errors/messages previously
                // alert(data.message);
            }
            else
            { 
                inputFiles.text(data.filename)
               // alert(data.message);
            }
        }).error(function(data, status, headers, config) {
            alert("Something Error in form process");
    });
  }

});

app.controller('Archived_Posting_Inventory_Controller', function(dataFactory, headerFooterData, headerFooterData2, fileUpload, $scope, $http, $location, $routeParams, $window, $timeout, $rootScope, $localStorage){

  $scope.segments = window.location.href.substr(window.location.href.lastIndexOf('/') + 1);
  $scope.itemPerPage = 5
  $scope.pageNumber = 1;
  $scope.libraryTemp = {};
  $scope.info = {};
  $scope.data = [];
  $scope.labels = '';
  $scope.innerContent = $routeParams.templateContent;  
  $scope.api = window.location.protocol + '//' + window.location.host + '/api';

  if($localStorage.loggedin == true) {
    headerFooterData2.getHeaderFooterData().then(function(data) {
      if(data.login === false) {
        $location.path('login').replace();
      } else {
        console.log(data);
        $scope.nav = data.menu;
      }
    });
  } else {
    headerFooterData.getHeaderFooterData().then(function(data) {
      if(data.login === false) {
        $location.path('login').replace();
      } else {
        console.log(data);
        $scope.nav = data.menu;
      }
    });
  }  

  getResultsPage($scope.pageNumber, '');
  function getResultsPage(pageNumber, search) {
      if(search != undefined && search != ''){
          dataFactory.httpRequest($scope.api + '/item/archived/?search=' + search + '&page=' + pageNumber).then(function(data) {
            $scope.data = data.data;
            $scope.totalItems = data.total;
            $scope.pageNumber = pageNumber;
          });
      }else{
          dataFactory.httpRequest($scope.api + '/item/archived/?page=' + pageNumber).then(function(data) {
            $scope.data = data.data;
            $scope.totalItems = data.total;
            $scope.pageNumber = pageNumber;
          });
      }
      console.log($scope.api + '/item/archived/?search=' + search + '&page=' + pageNumber);
  }

  $scope.pageChanged = function(newPage) {
    $scope.pageNumber = newPage;
    getResultsPage($scope.pageNumber, $scope.info.searchText);
  };

  $scope.search = function(){
    getResultsPage(1, $scope.info.searchText);
  }

  $scope.restoreItem = function(item, index){
    swal({
        title: 'Are you sure?',
        text: "You won't be able to revert this!",
        type: 'warning',
        showCancelButton: true,
        confirmButtonText: "Yes, restore it!",
    }).then(function(result) {
        if (result.value) {
            dataFactory.httpRequest($scope.api + '/item/restore/' + item, 'POST').then(function(data) {
              $scope.data.splice(index, 1);
              getResultsPage($scope.pageNumber, $scope.info.searchText);
            });
            swal(
                'Removed!',
                'The item has been successfully restored.',
                'success'
            );
        }
    });
  }
});

app.controller('Item_Controller', function(dataFactory, headerFooterData, headerFooterData2, $scope, $http, $location, $routeParams, $window, $timeout, $rootScope, $localStorage){

  $scope.itemPerPage = 5
  $scope.pageNumber = 1;
  $scope.pageNumber2 = 1;
  $scope.pageNumber3 = 1;
  $scope.libraryTemp = {};
  $scope.datas = {};
  $scope.data = [];
  $scope.profiles = $localStorage.credentials;
  $scope.page = $routeParams.pagename.replace(/-/g, " ");
  $scope.innerContent = $routeParams.templateContent;
  $scope.api = window.location.protocol + '//' + window.location.host + '/api';

  if($localStorage.loggedin == true) {
    headerFooterData2.getHeaderFooterData().then(function(data) {
      if(data.login === false) {
        $location.path('login').replace();
      } else {
        console.log(data);
        $scope.nav = data.menu;
      }
    });
  } else {
    headerFooterData.getHeaderFooterData().then(function(data) {
      if(data.login === false) {
        $location.path('login').replace();
      } else {
        console.log(data);
        $scope.nav = data.menu;
      }
    });
  }  

  $scope.item = {};
  $scope.resetForm = function() {
      var selectpicker = angular.element('.selectpicker');
      selectpicker.selectpicker('val', '');
      selectpicker.selectpicker('refresh');
      $scope.form = angular.copy($scope.item);
      
      getGL_Accounts();
  };
  $scope.resetForm();

  // $scope.days = [ 
  //   {id: 1, text: 'M'},
  //   {id: 2, text: 'T'},
  //   {id: 3, text: 'W'},
  //   {id: 4, text: 'TH'},
  //   {id: 5, text: 'F'},
  //   {id: 6, text: 'S'}
  // ];
  // $scope.sched = {
  //   days: [2, 4]
  // };



  angular.element(document).on('hidden.bs.modal', '.modal', function () {
      $scope.resetForm();
  });
  
  getGL_Accounts();
  getGroups();
  getResultsPage(1);
  function getResultsPage(pageNumber, search) {
      if(search != undefined && search != ''){
        console.log($scope.api + '/products/category/' + $scope.page + '?user_id=' + $scope.profiles.user_id + '&search=' + $scope.datas.searchText + '&page=' + pageNumber);
          dataFactory.httpRequest($scope.api + '/products/category/' + $scope.page + '?user_id=' + $scope.profiles.user_id + '&search=' + $scope.datas.searchText + '&page=' + pageNumber).then(function(data) {
            $scope.data = data.data;
            $scope.totalItems = data.total;
            $scope.pageNumber = pageNumber;
          });
      }else{
          console.log($scope.api + '/products/category/' + $scope.page + '?user_id=' + $scope.profiles.user_id + '&page=' + pageNumber);
          dataFactory.httpRequest($scope.api + '/products/category/' + $scope.page + '?user_id=' + $scope.profiles.user_id + '&page=' + pageNumber).then(function(data) {
            $scope.data = data.data;
            $scope.totalItems = data.total;
            $scope.pageNumber = pageNumber;
          });
      }
  }

  $scope.pageChanged = function(newPage) {
    getResultsPage(newPage, $scope.datas.searchText);
  };

  $scope.searchDB = function(){
    getResultsPage(1, $scope.datas.searchText);
  }

  itemPage($scope.pageNumber2, 0);
  function itemPage(pageNumber2, prod_Id, search = '') {
      if(search != undefined && search != ''){
          dataFactory.httpRequest($scope.api + '/products/product-item/' + prod_Id + '?search=' + $scope.datas.searchText + '&page=' + pageNumber2).then(function(data) {
            $scope.data_product = data.data_product;
            $scope.total_product = data.total_product;
            $scope.pageNumber2 = pageNumber2;
          });
      }else{
          dataFactory.httpRequest($scope.api + '/products/product-item/' + prod_Id + '?page=' + pageNumber2).then(function(data) {
            $scope.data_product = data.data_product;
            $scope.total_product = data.total_product;
            $scope.pageNumber2 = pageNumber2;
          });
      }
  }

  $scope.itemPageReload = function(prod_Id, index) {
    itemPage($scope.pageNumber2, prod_Id);
  };

  allItems($scope.pageNumber3, '');
  function allItems(pageNumber3, search = '') {
      if(search != undefined && search != ''){
          dataFactory.httpRequest($scope.api + '/products/all-item?search=' + $scope.datas.searchText + '&page=' + pageNumber3).then(function(data) {
            $scope.data_item = data.data_item;
            $scope.total_item = data.total_item;
            $scope.pageNumber3 = pageNumber3;
          });
      }else{
          dataFactory.httpRequest($scope.api + '/products/all-item?page=' + pageNumber3).then(function(data) {
            $scope.data_item = data.data_item;
            $scope.total_item = data.total_item;
            $scope.pageNumber3 = pageNumber3;
          });
      }
  }

  $scope.searchDB = function(){
    allItems(1, $scope.datas.searchItem);
  }

  function getGL_Accounts() {
      angular.element(document).ready(function () { 
          $http.get( $scope.api + '/item/display-all-active-gl-accounts' )
            .then(function(data) 
            {
              console.log(data);
              $scope.gl_accounts = data.data;
          });
      });
  }

  function getGroups() {
      angular.element(document).ready(function () { 
          $http.get( $scope.api + '/item/display-all-active-groups' )
            .then(function(data) 
            {
              console.log(data);
              $scope.groups = data.data;
          });
      });
  }

  // function getGL_Accounts() {
  //     angular.element('#gl_accounts_id').typeahead('destroy');
  //     angular.element(document).ready(function () { 
  //         $http.get( $scope.api + '/item/display-all-active-gl-accounts' )
  //           .then(function(data) 
  //           {
  //             console.log(data);
  //             var gl_accountsArray = data.data;
                    
  //             var gl_accounts = new Bloodhound({
  //                 datumTokenizer: Bloodhound.tokenizers.whitespace,
  //                 queryTokenizer: Bloodhound.tokenizers.whitespace,
  //                 local: gl_accountsArray
  //             });

  //             angular.element('#edit-product-modal #gl_accounts_id, #add-product-modal #gl_accounts_id').typeahead({
  //                 hint: true,
  //                 highlight: true,
  //                 minLength: 1
  //             },
  //             {
  //               name: 'gl_accounts',
  //               source: gl_accounts,
  //               length: 10
  //             }).bind('blur', function () {
  //                 if (gl_accountsArray.indexOf($(this).val()) === -1) {                            
  //                     if($(this).val() != "")
  //                     {   
  //                       angular.element('#gl_accounts_id').val('');
  //                     } else {
  //                     }
  //                 }
  //             });
  //         });
  //     });
  // }

  $scope.remove = function(item, index){
    swal({
        title: 'Are you sure?',
        text: "You won't be able to revert this!",
        type: 'warning',
        showCancelButton: true,
        confirmButtonText: "Yes, remove it!",
    }).then(function(result) {
        if (result.value) {
            dataFactory.httpRequest($scope.api + '/products/category/' + $scope.page + '/remove/' + item + '?user_id=' + $scope.profiles.user_id, 'GET').then(function(data) {
              $scope.data.splice(index,1);
              getResultsPage($scope.pageNumber);
            });
            swal(
                'Deleted!',
                'The product has been successfully removed.',
                'success'
            );
        }
    });
  }

  $scope.edit = function(id){
    dataFactory.httpRequest($scope.api + '/products/category/' + $scope.page + '/edit/' + id).then(function(data) {
        console.log(data);
        $scope.form = data;
        getGL_Accounts(); 
        angular.element(document).ready(function () { 
            $('select').selectpicker("render"); 
            $timeout(function () {
                angular.element('#edit-product-modal #gl_accounts_id').typeahead('val', data.gl_accounts_id);
            }, 100);
            if(data.product_img != '' && data.product_img != null) {
                angular.element('#edit-product-modal label.custom-file-label').text(data.product_img);
                angular.element('#edit-product-modal label.file-name').text(data.product_img);
            } else {
                angular.element('#edit-product-modal label.custom-file-label').text('Choose file');
                angular.element('#edit-product-modal label.file-name').text(data.product_img);
            }
        });
        
    });
  }

  $scope.saveProduct = function(){
    var files = angular.element('#add-product-modal label.file-name');
    console.log($scope.api + '/products/category/' + $scope.page + '/create?user_id=' + $scope.profiles.user_id + '&files=' + files.text());
    dataFactory.httpRequest($scope.api + '/products/category/' + $scope.page + '/create?user_id=' + $scope.profiles.user_id + '&files=' + files.text(),'POST',{},$scope.form).then(function(data) {
      $scope.data.push(data);
      $scope.info = data;
      console.log(data);
      if($scope.info.message) {
        swal(
            $scope.info.header,
            $scope.info.message,
            $scope.info.type
        );
      }
      var modal_element = angular.element('.modal');
      modal_element.modal('hide');
      getResultsPage(1);
    });
  }

  $scope.updateProduct = function(){
    dataFactory.httpRequest($scope.api + '/products/category/' + $scope.page + '/update/' + $scope.form.product_id + '?user_id=' + $scope.profiles.user_id, 'POST', {}, $scope.form).then(function(data) {
      $scope.data.push(data);
      $scope.info = data;
      console.log(data);
      if($scope.info.message) {
        swal(
            $scope.info.header,
            $scope.info.message,
            $scope.info.type
        );
      }
      var modal_element = angular.element('.modal');
      modal_element.modal('hide');
      getResultsPage(1);
    });
  }

  $scope.uploadedFile = function(element) {   
    $scope.$apply(function($scope) {
        $scope.files = element.files;  
    });
    
    var inputLabel = angular.element('label.custom-file-label');
    var inputFiles = angular.element('label.file-name');

    if(element.files.length != 0 ) {
        inputLabel.text(element.files[0].name);
    } else {
        inputLabel.text('Choose File');
    }

    $http({
        url: window.location.protocol + '//' + window.location.host + '/samsv2-api/products/upload',
        method: "POST",
        processData: false,
        headers: { 'Content-Type': undefined },
        data : $scope.formdata,
        transformRequest: function (data) {
            var formData = new FormData();
            var file = $scope.files[0];
            formData.append("file_upload",file); 
            return formData;  
            },
        }).success(function(data, status, headers, config) {
            $scope.status = data.status;

            if($scope.status == 1)
            {
                $scope.formdata = "";
                $scope.myform.$setPristine();
            }
            else
            { 
                inputFiles.text(data.filename)
            }
        }).error(function(data, status, headers, config) {
            alert("Something Error in form process");
    });
  }

});

app.controller('Item_Archived_Controller', function(dataFactory, headerFooterData, headerFooterData2, $scope, $http, $location, $routeParams, $window, $timeout, $rootScope, $localStorage){

  $scope.itemPerPage = 5
  $scope.pageNumber = 1;
  $scope.libraryTemp = {};
  $scope.datas = {};
  $scope.data = [];
  $scope.page = $routeParams.pagename.replace(/-/g, " ");
  $scope.innerContent = $routeParams.templateContent;
  $scope.api = window.location.protocol + '//' + window.location.host + '/api';

  if($localStorage.loggedin == true) {
    headerFooterData2.getHeaderFooterData().then(function(data) {
      if(data.login === false) {
        $location.path('login').replace();
      } else {
        console.log(data);
        $scope.nav = data.menu;
      }
    });
  } else {
    headerFooterData.getHeaderFooterData().then(function(data) {
      if(data.login === false) {
        $location.path('login').replace();
      } else {
        console.log(data);
        $scope.nav = data.menu;
      }
    });
  }  

  $scope.item = {};
  $scope.resetForm = function() {
      var selectpicker = angular.element('.selectpicker');
      selectpicker.selectpicker('val', '');
      selectpicker.selectpicker('refresh');
      $scope.form = angular.copy($scope.item);
  };
  $scope.resetForm();

  angular.element(document).on('hidden.bs.modal', '.modal', function () {
      $scope.resetForm();
  });
  
  getResultsPage(1);
  function getResultsPage(pageNumber, search) {
      if(search != undefined && search != ''){
          dataFactory.httpRequest($scope.api + '/products/category/' + $scope.page + '/archived?search=' + $scope.datas.searchText + '&page=' + pageNumber).then(function(data) {
            $scope.data = data.data;
            $scope.totalItems = data.total;
            $scope.pageNumber = pageNumber;
          });
      }else{
          dataFactory.httpRequest($scope.api + '/products/category/' + $scope.page + '/archived?page=' + pageNumber).then(function(data) {
            $scope.data = data.data;
            $scope.totalItems = data.total;
            $scope.pageNumber = pageNumber;
          });
      }
  }

  $scope.pageChanged = function(newPage) {
    getResultsPage(newPage, $scope.datas.searchText);
  };

  $scope.searchDB = function(){
    getResultsPage(1, $scope.datas.searchText);
  }

  $scope.restore = function(item, index){
    swal({
        title: 'Are you sure?',
        text: "You won't be able to revert this!",
        type: 'warning',
        showCancelButton: true,
        confirmButtonText: "Yes, restore it!",
    }).then(function(result) {
        if (result.value) {
            dataFactory.httpRequest($scope.api + '/products/category/' + $scope.page + '/restore/' + item, 'GET').then(function(data) {
              $scope.data.splice(index,1);
              getResultsPage($scope.pageNumber);
            });
            swal(
                'Deleted!',
                'The product has been successfully restored.',
                'success'
            );
        }
    });
  }

});

app.controller('KioskController', function(dataFactory, headerFooterData, headerFooterData2, $scope, $http, $location, $routeParams, $window, $document, $timeout, $rootScope, $localStorage){

  angular.element(document).ready(function () { 

      var isCtrl = false;

      $document.bind("keyup keypress", function (event) {
        if(event.which == 17) { isCtrl=false; }
      });
      
      $document.bind("keydown keypress", function (event) {

        // if(event.which == 116 || event.which == 82) {
        //   return false;
        // }

        if(event.which == 115) {
            var modal = angular.element('#search-product-modal');
            modal.modal({
                backdrop: 'static',
                keyboard: false,
            });
            modal.modal('show');            
        }

        if(event.which == 17) { isCtrl=true; }
        if(event.which == 115 && isCtrl == true) {
           return false;
        }
      });   
  });

  $scope.openSearch = function() {
    var modal = angular.element('#search-product-modal');
    modal.modal({
        backdrop: 'static',
        keyboard: false,
    });
    modal.modal('show');
  }

  var getData = function(){
    var json = [];
    $.each($window.sessionStorage, function(i, v){
      if ( !isNaN(i) && angular.isNumber(+i) || i == 0) {
        json.push(angular.fromJson(v));
      }
    });
    console.log(json);
    return json;
  }

  var getValue = function(){
    return $window.sessionStorage.length;
  }

  var getTotalPrice = function(){
    var total = 0;
    $.each($window.sessionStorage, function(i, v){
      var product = angular.fromJson(v);
      total += parseFloat(product.total_price);
    });
    return parseFloat(total).toFixed(2);
  }
  
  $scope.itemPerPage = 6;
  $scope.selectedItems = getData();
  $scope.selectedCount = getValue();
  $scope.totalPrice = getTotalPrice();
  $scope.totalPaid = parseFloat(0).toFixed(2);
  $scope.totalChange = ($scope.totalPaid > 0) ? parseFloat(parseFloat($scope.totalPrice) - parseFloat($scope.totalPaid)).toFixed(2) : parseFloat(0).toFixed(2); 
  $scope.validate = true;
  $scope.data = [];  
  $scope.counter = [];
  $scope.datas = {};
  $scope.pos = {};
  $scope.pageNumber = 1;
  $scope.libraryTemp = {};
  $scope.totalItemsTemp = {};
  $scope.checkoutItems = $window.sessionStorage.length;
  $scope.page = $routeParams.pagename;
  $scope.innerContent = $routeParams.templateContent;
  $scope.totalItems = 0;
  $scope.profiles = $localStorage.credentials;
  $scope.searches = [];
  $scope.balances = [];
  $scope.api = window.location.protocol + '//' + window.location.host + '/api';
  $scope.sams_api = window.location.protocol + '//' + window.location.host + '/samsv2';
  if(localStorage.getItem('locked') != null) {
    $scope.items = [];
    $scope.items = angular.fromJson(localStorage.getItem('locked'));
  } else {    
    $scope.items = [];
  }
  $scope.counterflow = false;
  console.log('counters: '+ $scope.counter);

  $scope.totalDiscountValue = function(num1, num2) {
    var total = parseFloat(num1).toFixed(2) - parseFloat(num2).toFixed(2);
    return '₱ ' + parseFloat(total).toFixed(2);
  }

  $scope.productPrice = function(num1) {
    return '₱ ' + parseFloat(num1).toFixed(2);
  }

  $scope.productDiscount = function(num1) {
    return '- ' + parseFloat(num1).toFixed(2);
  }

  $scope.checkLocked = function(id) {
    if(localStorage.getItem('locked') != null) {
      if(localStorage.getItem('locked').indexOf('"' + id + '"') >= 0  ) {
          return 1;
      } else {
          return 0;
      }
    }
  }

  headerFooterData2.getHeaderFooterData().then(function(data) {
      $scope.nav = data.menu;
  }); 

  getResultsPage($scope.pageNumber, '');
  function getResultsPage(pageNumber, search = '') {
    if(search == '') {
        console.log($scope.api + '/pos?products='+$scope.page+'&page='+pageNumber);
        dataFactory.httpRequest($scope.api + '/pos?products='+$scope.page+'&page='+pageNumber).then(function(data) {
          $scope.data = data.data;
          if(localStorage.getItem('locked') == null) {
            $scope.counter = data.data2;
          }
          $scope.totalItems = data.total;
          $scope.pageNumber = pageNumber;
        });
    } else {
        console.log($scope.api + '/pos?products=' + $scope.page + '&keywords=' + search + '&page=' + pageNumber);
        dataFactory.httpRequest($scope.api + '/pos?products=' + $scope.page + '&keywords=' + search + '&page='+pageNumber).then(function(data) {
          $scope.data = data.data;
          if(localStorage.getItem('locked') == null) {
            $scope.counter = data.data2;
          }
          $scope.totalItems = data.total;
          $scope.pageNumber = pageNumber;
        });
    }
  }

  $scope.pageChanged = function(newPage) {
    getResultsPage(newPage);
  }

  $scope.searchPOS = function()
  { 
      if($scope.datas.searchPOS.length >= 10){
        searchItem($scope.datas.searchPOS);
      }
  }

  function searchItem($item) {
    var hiddenText = angular.element('.hiddenText');
    dataFactory.httpRequest($scope.api + '/pos/search_code?item_code='+ $item).then(function(data) {
      $scope.dataResult = data;
      console.log($scope.dataResult);

      if($scope.dataResult.message == 'success')
      { 
        $scope.addItem(0, $scope.dataResult.product_id, $scope.dataResult.product_name, 1, $scope.dataResult.product_price, 1);
      }
      $timeout(function () {
        $scope.datas.searchPOS = '';
        hiddenText.focus();
      }, 100);
      $timeout(function () {
        hiddenText.focus();
      }, 1000);

    });
  }

  $scope.count = function (index, increment, quantity, id) {
      var counter = (localStorage.getItem(id) != null) ? localStorage.getItem(id) : 0
      if(quantity > 0 && (parseFloat(counter) + parseFloat($scope.data[index].product_counter))  != quantity) {
          $scope.data[index].product_counter = parseFloat($scope.data[index].product_counter) + parseFloat(increment);
      } 
      if( (parseFloat(counter) + parseFloat($scope.data[index].product_counter)) == quantity ) {
          $scope.counterflow = true;
      } else {
          $scope.counterflow = false;
      }
  }

  $scope.negate = function (index, increment) {
      if($scope.data[index].product_counter != 0) {
          $scope.data[index].product_counter -= increment;
      }
  }

  $scope.editItem = function ($index, id, name, quantity, price, total_price, discount) {
      $scope.checkout_items = { 
        index: $index + 1, 
        item: name, 
        price: parseFloat(price).toFixed(2), 
        quantity: quantity, 
        total: parseFloat( parseFloat(price) * parseFloat(quantity) ).toFixed(2), 
        discount: parseFloat(discount).toFixed(2)
      };
      $scope.resetItemCheckoutForm = function() {
          var selectpicker = angular.element('.selectpicker');
          selectpicker.selectpicker('val', '');
          selectpicker.selectpicker('refresh');
          $scope.checkout_item = angular.copy($scope.checkout_items);  
      };
      $scope.resetItemCheckoutForm();
  }

  $scope.checkItem = function() {
    var quantitys = angular.element('#checkout_quantity');
    var prices    = angular.element('#checkout_price');
    var totalText = angular.element('#checkout_total');
    var totals = (quantitys.val() > 0) ? parseFloat(prices.val()) * parseFloat(quantitys.val()) : 0; 
    $scope.checkout_item.total = parseFloat(totals).toFixed(2); 
    $scope.checkout_item.quantity = quantitys.val();
  }

  $scope.editCheckoutItem = function() {
    json = {
      name: $scope.checkout_item.item,
      quantity: $scope.checkout_item.quantity,
      price: parseFloat($scope.checkout_item.price),
      discount: parseFloat($scope.checkout_item.discount).toFixed(2),
      total_price : parseFloat( (parseFloat($scope.checkout_item.quantity) * parseFloat($scope.checkout_item.price))  ).toFixed(2)
    }
    $window.sessionStorage.setItem($scope.checkout_item.index - 1, JSON.stringify(json));
    $scope.selectedItems = getData();
    $scope.selectedCount = getValue();
    $scope.totalPrice = getTotalPrice();
    var modal_element = angular.element('.modal');
    modal_element.modal('hide');
  }

  $scope.addItem = function(index, id, name, quantity, price, counter = '', discount = ''){
      if(counter == '') {
        $scope.data[index].product_counter = 0;
        $scope.counter[index].product_counter = 0;
      }
      if(quantity > 0) {
        json = {
          id: id,
          name: name,
          quantity: quantity,
          price: parseFloat(parseFloat(price) - parseFloat(discount)).toFixed(2),
          discount: discount,
          total_price : parseFloat( (parseFloat(quantity) * parseFloat(price)) - (parseFloat(quantity) * parseFloat(discount))  ).toFixed(2)
        }
        if($window.sessionStorage.length == 0) {
          var indexs = 0;
        } else {
          var indexs = $window.sessionStorage.length;
        }
        
        if($scope.counterflow == true) {
            $scope.items.push(id);
            localStorage.setItem("locked", JSON.stringify($scope.items));
        }

        if(localStorage.getItem(id) != null){
          var old_quantity = localStorage.getItem(id);
          var new_quantity = parseFloat(old_quantity) + parseFloat(quantity);
          localStorage.setItem(id, new_quantity);
        } else {
          localStorage.setItem(id, quantity);
        }

        $scope.counter[index].product_counter = parseFloat($scope.counter[index].product_counter) + parseFloat(quantity);
        $scope.data[index].product_counter = 0;
        $window.sessionStorage.setItem(indexs, JSON.stringify(json));
        $scope.selectedItems = getData();
        $scope.selectedCount = getValue();
        $scope.totalPrice = getTotalPrice();    
        getResultsPage($scope.pageNumber, '');
        reloadCheckout();
        console.log('counter: ' + $scope.counter[index].product_counter + ', data:' + $scope.data[index].product_counter);
        console.log('item added successfully!');
      }
  }

  $scope.removeItem = function(id){
    $window.sessionStorage.removeItem(id);
    $scope.selectedItems = getData();
    $scope.selectedCount = getValue();
    $scope.totalPrice = getTotalPrice();
    console.log('item removed successfully!');
  }

  $scope.saveAdd = function(){
    dataFactory.httpRequest('itemsCreate','POST',{},$scope.form).then(function(data) {
      $scope.data.push(data);
      $(".modal").modal("hide");
      
      $scope.pageNumber = 1;
      getResultsPage(1);
    });
  }

  $scope.edit = function(id){
    dataFactory.httpRequest('itemsEdit/'+id).then(function(data) {
      console.log(data);
        $scope.form = data;
    });
  }

  $scope.saveEdit = function(){
    dataFactory.httpRequest('itemsUpdate/'+$scope.form.id,'PUT',{},$scope.form).then(function(data) {
        $(".modal").modal("hide");
        $scope.data = apiModifyTable($scope.data,data.id,data);
    });
  }

  $scope.remove = function(item,index){
    var result = confirm("Are you sure delete this item?");
    if (result) {
      dataFactory.httpRequest('itemsDelete/'+item.id,'DELETE').then(function(data) {
          $scope.data.splice(index,1);
          getResultsPage($scope.pageNumber);
      });
    }
  }

  $scope.removeAll = function(){
    sessionStorage.clear();
    $scope.items = [];
    $.each(localStorage, (key) => {
      if ( !isNaN(key) && angular.isNumber(+key) || key == 0) {
        localStorage.removeItem(key);
      }
    });
    localStorage.removeItem("locked");
    $scope.searches = [];
    $scope.selectedItems = getData();
    $scope.selectedCount = getValue();
    $scope.totalPrice = getTotalPrice();
    reloadCheckout();
    $timeout(function () {        
        getResultsPage($scope.pageNumber);
    }, 100);
  }
   
  $scope.checkOUT = function()
  {   
      var $type    = $scope.form.type;
      var $stud_no = ($scope.searches.length != 0 && $scope.searches[0].searches == 'true') ? $scope.searches[0].stud_no : '';
      var json = [];

      angular.forEach($window.sessionStorage, function(i, v){
          json.push(i);
      });  

      $scope.pos = angular.copy(json); 

      if($type == 2 && $scope.searches[0].searches == 'false' && $scope.searches.info.length > 0) { 
      } else {
        swal({
            title: 'Are you sure?',
            text: 'the transaction will be saved.',
            showCancelButton: true,
            confirmButtonText: 'Yes please!',
            cancelButtonText: 'No thanks!',
            confirmButtonColor: '#dc2430',
            cancelButtonColor: '#7b4397',
            showLoaderOnConfirm: true,
            preConfirm: function (isConfirm) {
                return new Promise(function (resolve, reject) {
                    if (isConfirm) {
                        $timeout(function () {
                            
                            console.log($scope.api + '/pos/order?user_id=0&type=' + $type + '&stud_no=' + $stud_no);
                            dataFactory.httpRequest($scope.api + '/pos/order?user_id=0&type=' + $type + '&stud_no=' + $stud_no,'POST',{},$scope.pos).then(function(data) {
                                $scope.data.push(data);
                                var total_payments = data.total_pay;
                                var trans_no = data.trans_no;

                                console.log($scope.api + '/customer/deduct-credit?stud_no=' + $stud_no + '&total_payments=' + total_payments + "&trans_num=" + trans_no);
                                if($type == 2) {
                                  dataFactory.httpRequest($scope.api + '/customer/deduct-credit?stud_no=' + $stud_no + '&total_payments=' + total_payments + "&trans_num=" + trans_no,'GET',{},'').then(function(datas) {
                                      console.log(datas);
                                  });
                                }

                                $scope.dialog = data;
                                console.log(data);
                                if($scope.dialog.message) {
                                  swal({
                                      title: $scope.dialog.header,
                                      text:  $scope.dialog.message,
                                      type:  $scope.dialog.type,
                                      timer: 2000,
                                      showConfirmButton: false
                                  }).then(function(result) {
                                      if (result.dismiss === 'timer') {
                                          window.onkeydown = null;
                                          window.onfocus = null; 
                                          var hiddenText = angular.element('.hiddenText');
                                          hiddenText.focus();
                                          console.log('I was closed by the timer')
                                      }
                                  });
                                }       
                            });

                            $scope.removeAll();
                            var modal_element = angular.element('.modal');
                            modal_element.modal('hide');         

                           resolve();
                        }, 1000);
                    }
                })
            },
            allowOutsideClick: false
            }).then(function (isConfirm) {     
                $timeout(function () {
                }, 100);
        },  function (dismiss) {
        }).catch(swal.noop); 
      }
  }

  $scope.forms = { type: 1 };
  $scope.resetForm = function() {
      var selectpicker = angular.element('.selectpicker');
      selectpicker.selectpicker('val', '');
      selectpicker.selectpicker('refresh');
      $scope.form = angular.copy($scope.forms);
      $scope.searches = [];
      $scope.balances = [];  
      reloadCheckout();    
  };
  $scope.resetForm();

  $scope.openCheckout = function() {
      if($scope.totalPrice != 0) { 
        $scope.resetForm();
        var modal = angular.element('#kiosk-checkout-modal');
        modal.modal({
          backdrop: 'static',
          keyboard: false,
        });
        modal.modal('show');
      }
  }

  $scope.openAmountPaid = function() {
      var modal = angular.element('#edit-checkout-amount-modal');
      modal.modal('show');
      $scope.checkout_amounts = { totalPrice: (parseFloat($scope.totalPrice)- parseFloat($scope.totalDiscount)), totalPaid: $scope.totalPaid, totalChange: $scope.totalChange, totalDiscount: $scope.totalDiscount };
      $scope.checkout_amount = angular.copy($scope.checkout_amounts);
      $timeout(function () {
        var paid = angular.element('body #edit-checkout-amount-modal input[name="totalPaid"]');
        paid.focus();
      }, 500);
  }

  function reloadCheckout()
  {
    $scope.totalPrice = getTotalPrice();
    $scope.totalPaid = parseFloat(0).toFixed(2);
    $scope.totalChange = ($scope.totalPaid > 0) ? parseFloat(parseFloat($scope.totalPaid) - parseFloat($scope.totalPrice)).toFixed(2) : parseFloat(0).toFixed(2);
    $scope.checkoutItems = $window.sessionStorage.length;    
  }

  $scope.order_mode = function() {
    if($scope.form.type == 1) {
      $scope.totalPaid = parseFloat(0).toFixed(2);
      $scope.totalChange = parseFloat(0).toFixed(2);
      $scope.validate = true;
    } else {
      if($scope.searches.length != 0) {
        if($scope.searches[0].searches == 'true') {
          $scope.totalPaid = parseFloat($scope.searches[0].credits).toFixed(2);
          $scope.totalChange = ($scope.totalPaid > 0) ? parseFloat(parseFloat($scope.totalPaid) - parseFloat($scope.totalPrice)).toFixed(2) : parseFloat(0).toFixed(2);
          if(parseFloat($scope.totalPaid) >= parseFloat($scope.totalPrice)) {
            $scope.validate = true; 
          } else {
            $scope.validate = false;
          }
        } else {
          $scope.totalPaid = parseFloat(0).toFixed(2);
          $scope.validate = false;
        }
      } else {
        $scope.totalPaid = parseFloat(0).toFixed(2);
        $scope.validate = false;
      }
    }

  }

  $scope.clearKeywords = function(clear = '') {
    var keywords = angular.element('#keywords');
    $scope.form.keywords = '';
    $scope.searches = [];
    if(clear != '') {
      keywords.focus();
    }
  }

  $scope.searchKeywords = function() {
    var keywords = angular.element('#keywords').val();
    if(keywords.length > 0) {
      dataFactory.httpRequest($scope.sams_api + '/api/search_member_via_keywords/' + keywords).then(function(data) {
          console.log(data.info[0].searches);
          if(data.info[0].searches == 'true'){
            $scope.searches = data;
            dataFactory.httpRequest($scope.api + '/api/pos/validate_transactions/' + keywords).then(function(data2) {
                if(data2.transactions > 0) {
                  $scope.totalDiscount  = parseFloat(0).toFixed(2);
                } else {
                  $scope.totalDiscount  = parseFloat($scope.searches[0].discounts).toFixed(2);
                }
                console.log('discounts: ' + $scope.totalDiscount);
                if($scope.form.type == 2) {
                  $scope.totalPaid = parseFloat($scope.searches[0].credits).toFixed(2);
                  $scope.totalChange = ($scope.totalPaid > 0) ? parseFloat(parseFloat($scope.totalPaid) - parseFloat($scope.totalPrice)).toFixed(2) : parseFloat(0).toFixed(2);
                  
                  if(parseFloat($scope.totalPaid) >= parseFloat($scope.totalPrice)) {
                    $scope.validate = true; 
                  } else {
                    $scope.validate = false;
                  }
                }
            });
          } else {
            $scope.searches = [];
            $scope.totalPaid  = parseFloat(0).toFixed(2);
            $scope.totalChange  = parseFloat(0).toFixed(2);
            $scope.validate = false;
          }
      });
    }
    else
    {
      $scope.searches = [];
      $scope.totalPaid  = parseFloat(0).toFixed(2);
      $scope.totalChange  = parseFloat(0).toFixed(2);
    }
  }

  $scope.concat_btn = function(element) {   
      if(element.currentTarget.value != 'clr') {
        if(element.currentTarget.value != '.'){
          $scope.checkout_item.quantity = $scope.checkout_item.quantity + "" + element.currentTarget.value;
        }else {
          if($scope.checkout_item.quantity.indexOf('.') === -1) {
            $scope.checkout_item.quantity = $scope.checkout_item.quantity + "" + element.currentTarget.value;
          }
        }
      } else {
        var str = $scope.checkout_item.quantity.toString();
        $scope.checkout_item.quantity = str.slice(0, -1);
      }

      if($scope.checkout_item.quantity != ''){
        $scope.checkout_item.total = parseFloat(parseFloat($scope.checkout_item.quantity) * parseFloat($scope.checkout_item.price)).toFixed(2);
      } else {
        $scope.checkout_item.total = parseFloat(0).toFixed(2);
      }
  }

  $scope.OpenBalance = function() {
      var modal = angular.element('#balance-modal');
      modal.modal('show');
  }

  $scope.clearBalance = function(clear = '') {
    var keywords = angular.element('body #balance-modal input[name="keywords"]');
    $scope.form.keywords = '';
    $scope.searches = [];
    $scope.balances = [];
    if(clear != '') {      
      keywords.focus();
    }
  }

  $scope.searchBalance = function() {
    var keywords = angular.element('#balance-modal input[name="keywords"]').val();
    console.log($scope.sams_api + '/api/search_member_via_keywords/' + keywords);
    if(keywords.length > 0) {
      dataFactory.httpRequest($scope.sams_api + '/api/search_member_via_keywords/' + keywords).then(function(data) {
          console.log(data.info[0].searches);
          if(data.info[0].searches == 'true'){
            $scope.balances = data;
          } else {
            $scope.balances = [];
          }
      });
    }
    else
    {
      $scope.balances = [];
    }
  }

  $scope.searchProduct = function()
  {   
      getResultsPage($scope.pageNumber, $scope.datas.searchProduct);
  }

  $scope.clearProduct = function(clear = '') {
    var keywords = angular.element('body #search-product-modal input[name="keywords"]');
    $scope.datas.searchProduct = '';
    $timeout(function () {
       keywords.focus();
    });   
  }

}); 

app.controller('POSController', function(dataFactory, headerFooterData, headerFooterData2, $scope, $http, $location, $routeParams, $window, $document, $timeout, $rootScope, $localStorage){

  var getData = function(){
    var json = [];
    $.each($window.sessionStorage, function(i, v){
      if ( !isNaN(i) && angular.isNumber(+i) || i == 0) {
        json.push(angular.fromJson(v));
      }
    });
    console.log(json);
    return json;
  }

  var getValue = function(){
    return $window.sessionStorage.length;
  }

  var getTotalPrice = function(){
    var total = 0;
    $.each($window.sessionStorage, function(i, v){
      var product = angular.fromJson(v);
      total += parseFloat(product.total_price);
    });
    return parseFloat(total).toFixed(2);
  }

  $scope.itemPerPage = 9;
  $scope.totalDiscount = 0;
  $scope.selectedItems = getData();
  $scope.selectedCount = getValue();
  $scope.totalPrice = getTotalPrice();
  $scope.totalPaid = parseFloat(0).toFixed(2);
  $scope.totalChange = ($scope.totalPaid > 0) ? parseFloat(parseFloat($scope.totalPrice) - parseFloat($scope.totalPaid)).toFixed(2) : parseFloat(0).toFixed(2); 
  $scope.validate = false;
  $scope.data = [];
  $scope.counter = [];
  $scope.datas = {};
  $scope.pos = {};
  $scope.pageNumber = 1;
  $scope.libraryTemp = {};
  $scope.totalItemsTemp = {};
  $scope.checkoutItems = $window.sessionStorage.length;
  $scope.page = $routeParams.pagename;
  $scope.innerContent = $routeParams.templateContent;
  $scope.totalItems = 0;
  $scope.profiles = $localStorage.credentials;
  $scope.searches = [];
  $scope.balances = [];
  $scope.api = window.location.protocol + '//' + window.location.host + '/api';
  $scope.sams_api = window.location.protocol + '//' + window.location.host + '/samsv2';
  $scope.PendingData = [];
  $scope.PendingItems = '';
  $scope.PendingPageNumber = 1;
  $scope.ReservedData = [];
  $scope.ReservedItems = '';
  $scope.ReservedPageNumber = 1;
  $scope.ServedData = [];
  $scope.ServedItems = '';
  $scope.ServedPageNumber = 1;
  $scope.CancelledData = [];
  $scope.CancelledItems = '';
  $scope.CancelledPageNumber = 1;
  $scope.transType = '';
  $scope.transNum  = '';
  if(localStorage.getItem('locked') != null) {
    $scope.items = [];
    $scope.items = angular.fromJson(localStorage.getItem('locked'));
  } else {
    $scope.items = [];
  }
  $scope.counterflow = false;
  console.log($scope.innerContent);

  $scope.model = {
    barcode: 'none',
  };
    
  $scope.barcodeScanned = function(barcode) {        
      console.log('callback received barcode: ' + barcode);                     
      $scope.model.barcode = barcode; 
      if (angular.element('#balance-modal').hasClass('show')) {
        searchBarcode2(barcode);
      } else {    
        searchBarcode(barcode);
      }
  };  

  function searchBarcode(keywords) {
    angular.element('#checkout-modal input[name="keywords"]').val(keywords);
    if(keywords.length > 0) {
      console.log($scope.api + '/customer/search-member-via-keywords/' + keywords);
      dataFactory.httpRequest($scope.api + '/customer/search-member-via-keywords/' + keywords).then(function(data) {
          console.log(data[0].searches);
          if(data[0].searches == 'true'){
            $scope.searches = data;
            console.log($scope.api + '/pos/validate-transactions/' + keywords);
            dataFactory.httpRequest($scope.api + '/pos/validate-transactions/' + keywords).then(function(data2) {
                // if(data2.transactions > 0) {
                //   $scope.totalDiscount  = parseFloat(0).toFixed(2);
                // } else {
                //   $scope.totalDiscount  = parseFloat($scope.searches[0].discounts).toFixed(2);
                // }
                // console.log('discounts: ' + $scope.totalDiscount);
                if($scope.form.type == 2) {
                  $scope.totalPaid = parseFloat($scope.searches[0].credits).toFixed(2);
                  $scope.totalChange = ($scope.totalPaid > 0) ? parseFloat(parseFloat($scope.totalPaid) - parseFloat($scope.totalPrice)).toFixed(2) : parseFloat(0).toFixed(2);
                  
                  if(parseFloat($scope.totalPaid) >= parseFloat($scope.totalPrice)) {
                    $scope.validate = true; 
                  } else {
                    $scope.validate = false;
                  }
                }
            });

            // $scope.totalDiscount  = parseFloat($scope.searches[0].discounts).toFixed(2);
            // console.log('discounts: ' + $scope.totalDiscount);
            // if($scope.form.payment == 2) {
            //   $scope.totalPaid = parseFloat($scope.searches[0].credits).toFixed(2);
            //   $scope.totalChange = ($scope.totalPaid > 0) ? parseFloat(parseFloat($scope.totalPaid) - parseFloat($scope.totalPrice)).toFixed(2) : parseFloat(0).toFixed(2);
            //   if(parseFloat($scope.totalPaid) >= parseFloat($scope.totalPrice)) {
            //     $scope.validate = true; 
            //   } else {
            //     $scope.validate = false;
            //   }
            // }
          } else {
            $scope.searches = [];
            $scope.totalPaid  = parseFloat(0).toFixed(2);
            $scope.totalChange  = parseFloat(0).toFixed(2);
            $scope.totalDiscount  = parseFloat(0).toFixed(2);
            $scope.validate = false;
          }
      });
    }
    else
    {
      $scope.searches = [];
      $scope.totalPaid  = parseFloat(0).toFixed(2);
      $scope.totalChange  = parseFloat(0).toFixed(2);
      $scope.totalDiscount  = parseFloat(0).toFixed(2);
    }
  }

  function searchBarcode2(keywords) {
    angular.element('#balance-modal input[name="keywords"]').val(keywords);
    console.log($scope.api + '/customer/search-member-via-keywords/' + keywords);
    if(keywords.length > 0) {
      dataFactory.httpRequest($scope.api + '/customer/search-member-via-keywords/' + keywords).then(function(data) {
          console.log(data[0].searches);
          if(data[0].searches == 'true'){
            $scope.balances = data;
          } else {
            $scope.balances = [];
          }
      });
    }
    else
    {
      $scope.balances = [];
    }
  }

  $scope.totalDiscountValue = function(num1, num2) {
    var total = parseFloat(num1).toFixed(2) - parseFloat(num2).toFixed(2);
    return '₱ ' + parseFloat(total).toFixed(2);
  }

  $scope.productPrice = function(num1) {
    return '₱ ' + parseFloat(num1).toFixed(2);
  }

  $scope.productDiscount = function(num1) {
    return '- ' + parseFloat(num1).toFixed(2);
  }

  $scope.checkLocked = function(id) {
    if(localStorage.getItem('locked') != null) {
      if(localStorage.getItem('locked').indexOf('"' + id + '"') >= 0  ) {
          return 1;
      } else {
          return 0;
      }
    }
  }

  if($localStorage.loggedin == true) {
    headerFooterData2.getHeaderFooterData().then(function(data) {
      if(data.login === false) {
        $location.path('login').replace();
      } else {
        console.log(data);
        $scope.nav = data.menu;
      }
    });
  } else {
    headerFooterData.getHeaderFooterData().then(function(data) {
      if(data.login === false) {
        $location.path('login').replace();
      } else {
        console.log(data);
        $scope.nav = data.menu;
      }
    });
  }  

  // getPendingNotifications(1, '');
  function getPendingNotifications(pageNumber, search = '') {
    if(search == '') {
        console.log($scope.api + '/pos/get-all-pending-notifications?page='+pageNumber);
        dataFactory.httpRequest($scope.api + '/pos/get-all-pending-notifications?page='+pageNumber).then(function(data) {
          $scope.PendingData = data.data;
          $scope.PendingItems = data.total;
          $scope.PendingPageNumber = pageNumber;
        });
    } else {
        console.log($scope.api + '/pos/get-all-pending-notifications?keywords=' + search + '&page='+pageNumber);
        dataFactory.httpRequest($scope.api + '/pos/get-all-pending-notifications?keywords=' + search + '&page='+pageNumber).then(function(data) {
          $scope.PendingData = data.data;
          $scope.PendingItems = data.total;
          $scope.PendingPageNumber = pageNumber;
        });
    }
  }

  $scope.searchPending = function()
  { 
      getPendingNotifications($scope.PendingPageNumber, $scope.datas.searchPending);
  }

  // getReservedNotifications(1, '');
  function getReservedNotifications(pageNumber, search = '') {
    if(search == '') {
        console.log($scope.api + '/pos/get-all-reserved-notifications?page='+pageNumber);
        dataFactory.httpRequest($scope.api + '/pos/get-all-reserved-notifications?page='+pageNumber).then(function(data) {
          $scope.ReservedData = data.data;
          $scope.ReservedItems = data.total;
          $scope.ReservedPageNumber = pageNumber;
        });
    } else {
        console.log($scope.api + '/pos/get-all-reserved-notifications?keywords=' + search + '&page='+pageNumber);
        dataFactory.httpRequest($scope.api + '/pos/get-all-reserved-notifications?keywords=' + search + '&page='+pageNumber).then(function(data) {
          $scope.ReservedData = data.data;
          $scope.ReservedItems = data.total;
          $scope.ReservedPageNumber = pageNumber;
        });
    }
  }

  $scope.searchReserved = function()
  { 
      getReservedNotifications($scope.ReservedPageNumber, $scope.datas.searchReserved);
  }

  // getServedNotifications(1, '');
  function getServedNotifications(pageNumber, search = '') {
    if(search == '') {
        console.log($scope.api + '/pos/get-all-served-notifications?page='+pageNumber);
        dataFactory.httpRequest($scope.api + '/pos/get-all-served-notifications?page='+pageNumber).then(function(data) {
          $scope.ServedData = data.data;
          $scope.ServedItems = data.total;
          $scope.ServedPageNumber = pageNumber;
        });
    } else {
        console.log($scope.api + '/pos/get-all-served-notifications?keywords=' + search + '&page='+pageNumber);
        dataFactory.httpRequest($scope.api + '/pos/get-all-served-notifications?keywords=' + search + '&page='+pageNumber).then(function(data) {
          $scope.ServedData = data.data;
          $scope.ServedItems = data.total;
          $scope.ServedPageNumber = pageNumber;
        });
    }
  }

  $scope.searchServed = function()
  { 
      getServedNotifications($scope.ServedPageNumber, $scope.datas.searchServed);
  }

  // getCancelledNotifications(1);
  function getCancelledNotifications(pageNumber, search = '') {
    if(search == '') {
        console.log($scope.api + '/pos/get-all-cancelled-notifications?page='+pageNumber);
        dataFactory.httpRequest($scope.api + '/pos/get-all-cancelled-notifications?page='+pageNumber).then(function(data) {
          $scope.CancelledData = data.data;
          $scope.CancelledItems = data.total;
          $scope.CancelledPageNumber = pageNumber;
        });
    } else {
        console.log($scope.api + '/pos/get-all-cancelled-notifications?keywords=' + search + '&page='+pageNumber);
        dataFactory.httpRequest($scope.api + '/pos/get-all-cancelled-notifications?keywords=' + search + '&page='+pageNumber).then(function(data) {
          $scope.CancelledData = data.data;
          $scope.CancelledItems = data.total;
          $scope.CancelledPageNumber = pageNumber;
        });
    }
  }

  $scope.searchCancelled = function()
  { 
      getCancelledNotifications($scope.CancelledPageNumber, $scope.datas.searchCancelled);
  }
  
  $scope.resetNotif = function() {
    getPendingNotifications(1, $scope.datas.searchPending = '');
    getReservedNotifications(1, $scope.datas.searchReserved = '');
    getServedNotifications(1, $scope.datas.searchServed = '');
    getCancelledNotifications(1, $scope.datas.searchCancelled = '');
  }

  $scope.pageChanged = function(newPage) {
    getResultsPage(newPage);
  }

  $scope.searchPOS = function()
  { 
      if($scope.datas.searchPOS.length >= 0){
        searchItem($scope.datas.searchPOS);
      }
  }

  getResultsPage(1);
  function getResultsPage(pageNumber) {
      if(! $.isEmptyObject($scope.libraryTemp)){
          console.log($scope.api + '/pos?user_id=' + $scope.profiles.user_id + '&products='+$scope.page+'&search='+$scope.searchText+'&page='+pageNumber);
          dataFactory.httpRequest($scope.api + '/pos?user_id=' + $scope.profiles.user_id + '&products='+$scope.page+'&search='+$scope.searchText+'&page='+pageNumber).then(function(data) {
            $scope.data = data.data;
            $scope.counter = data.data2;
            $scope.totalItems = data.total;
            $scope.pageNumber = pageNumber;
          });
      }else{
        console.log($scope.api + '/pos?user_id=' + $scope.profiles.user_id + '&products='+$scope.page+'&page='+pageNumber);
        dataFactory.httpRequest($scope.api + '/pos?user_id=' + $scope.profiles.user_id + '&products='+$scope.page+'&page='+pageNumber).then(function(data) {
          $scope.data = data.data;
          $scope.counter = data.data2;
          $scope.totalItems = data.total;
          $scope.pageNumber = pageNumber;
        });
      }
  }

  $scope.count = function (index, increment, quantity, id) {
      var counter = (localStorage.getItem(id) != null) ? localStorage.getItem(id) : 0
      if(quantity > 0 && (parseFloat(counter) + parseFloat($scope.data[index].product_counter))  != quantity) {
          $scope.data[index].product_counter = parseFloat($scope.data[index].product_counter) + parseFloat(increment);
      } 
      if( (parseFloat(counter) + parseFloat($scope.data[index].product_counter)) == quantity ) {
          $scope.counterflow = true;
      } else {
          $scope.counterflow = false;
      }
  }

  $scope.negate = function (index, increment) {
      if($scope.data[index].product_counter != 0) {
          $scope.data[index].product_counter -= increment;
      }
  }

  $scope.editItem = function ($index, id, name, quantity, price, total_price, discount) {
      $scope.checkout_items = { 
        index: $index + 1, 
        item: name, 
        price: parseFloat(price).toFixed(2), 
        quantity: quantity, 
        total: parseFloat( parseFloat(price) * parseFloat(quantity) ).toFixed(2), 
        discount: parseFloat(discount).toFixed(2)
      };
      $scope.resetItemCheckoutForm = function() {
          var selectpicker = angular.element('.selectpicker');
          selectpicker.selectpicker('val', '');
          selectpicker.selectpicker('refresh');
          $scope.checkout_item = angular.copy($scope.checkout_items);  
      };
      $scope.resetItemCheckoutForm();
  }

  $scope.checkItem = function() {
    var quantitys = angular.element('#checkout_quantity');
    var prices    = angular.element('#checkout_price');
    var totalText = angular.element('#checkout_total');
    var totals = (quantitys.val() > 0) ? parseFloat(prices.val()) * parseFloat(quantitys.val()) : 0; 
    $scope.checkout_item.total = parseFloat(totals).toFixed(2); 
    $scope.checkout_item.quantity = quantitys.val();
  }

  $scope.editCheckoutItem = function() {
    json = {
      name: $scope.checkout_item.item,
      quantity: $scope.checkout_item.quantity,
      price: parseFloat($scope.checkout_item.price),
      discount: parseFloat($scope.checkout_item.discount).toFixed(2),
      total_price : parseFloat( (parseFloat($scope.checkout_item.quantity) * parseFloat($scope.checkout_item.price))  ).toFixed(2)
    }
    $window.sessionStorage.setItem($scope.checkout_item.index - 1, JSON.stringify(json));
    $scope.selectedItems = getData();
    $scope.selectedCount = getValue();
    $scope.totalPrice = getTotalPrice();
    var modal_element = angular.element('.modal');
    modal_element.modal('hide');
  }

  function searchItem($item) {
    var hiddenText = angular.element('body input.hiddenText');
    dataFactory.httpRequest($scope.api + '/pos/search_code?item_code='+ $item).then(function(data) {
      $scope.dataResult = data;
      console.log($scope.dataResult);

      if($scope.dataResult.message == 'success')
      { 
        $scope.addItem(0, $scope.dataResult.product_id, $scope.dataResult.product_name, 1, $scope.dataResult.product_price, 1, $scope.dataResult.product_discount);
      }
      $timeout(function () {
        $scope.datas.searchPOS = '';
        hiddenText.focus();
      }, 100);
      $timeout(function () {
        hiddenText.focus();
      }, 1000);

    });
  }

  $scope.addItem = function(index, id, name, quantity, price, counter = '', discount = ''){
      // alert('index: ' + index + ', id: ' + id + ', name: ' + name + ', quantity: ' + quantity + ', price: ' + price + ', counter: ' + counter + ', discount: ' + discount);
      if(counter == '') {
        $scope.data[index].product_counter = 0;
        $scope.counter[index].product_counter = 0;
      }

      if(quantity > 0) {
        json = {
          id: id,
          name: name,
          quantity: quantity,
          price: parseFloat(parseFloat(price) - parseFloat(discount)).toFixed(2),
          discount: discount,
          total_price : parseFloat( (parseFloat(quantity) * parseFloat(price)) - (parseFloat(quantity) * parseFloat(discount))  ).toFixed(2)
        }
        if($window.sessionStorage.length == 0) {
          var indexs = 0;
        } else {
          var indexs = $window.sessionStorage.length;
        }
        
        if($scope.counterflow == true) {
            $scope.items.push(id);
            localStorage.setItem("locked", JSON.stringify($scope.items));
        }

        if(localStorage.getItem(id) != null){
          var old_quantity = localStorage.getItem(id);
          var new_quantity = parseFloat(old_quantity) + parseFloat(quantity);
          localStorage.setItem(id, new_quantity);
        } else {
          localStorage.setItem(id, quantity);
        }
        
        $scope.counter[index].product_counter = parseFloat($scope.counter[index].product_counter) + parseFloat(quantity);
        $scope.data[index].product_counter = 0;
        $window.sessionStorage.setItem(indexs, JSON.stringify(json));
        $scope.selectedItems = getData();
        $scope.selectedCount = getValue();
        $scope.totalPrice = getTotalPrice();    
        getResultsPage($scope.pageNumber, '');
        reloadCheckout();
        console.log('counter: ' + $scope.counter[index].product_counter + ', data:' + $scope.data[index].product_counter);
        console.log('item added successfully!');
      }
  }

  $scope.removeItem = function(id){
    $window.sessionStorage.removeItem(id);
    $scope.selectedItems = getData();
    $scope.selectedCount = getValue();
    $scope.totalPrice = getTotalPrice();
    console.log('item removed successfully!');
  }

  $scope.searchDB = function(){
      if($scope.searchText.length >= 3){
          if($.isEmptyObject($scope.libraryTemp)){
              $scope.libraryTemp = $scope.data;
              $scope.totalItemsTemp = $scope.totalItems;
              $scope.data = {};
          }
          getResultsPage(1);
      }else{
          if(! $.isEmptyObject($scope.libraryTemp)){
              $scope.data = $scope.libraryTemp ;
              $scope.totalItems = $scope.totalItemsTemp;
              $scope.libraryTemp = {};
          }
      }
  }

  $scope.saveAdd = function(){
    dataFactory.httpRequest('itemsCreate','POST',{},$scope.form).then(function(data) {
      $scope.data.push(data);
      $(".modal").modal("hide");
      
      $scope.pageNumber = 1;
      getResultsPage(1);
    });
  }

  $scope.edit = function(id){
    dataFactory.httpRequest('itemsEdit/'+id).then(function(data) {
    	console.log(data);
      	$scope.form = data;
    });
  }

  $scope.saveEdit = function(){
    dataFactory.httpRequest('itemsUpdate/'+$scope.form.id,'PUT',{},$scope.form).then(function(data) {
      	$(".modal").modal("hide");
        $scope.data = apiModifyTable($scope.data,data.id,data);
    });
  }

  $scope.remove = function(item,index){
    var result = confirm("Are you sure delete this item?");
   	if (result) {
      dataFactory.httpRequest('itemsDelete/'+item.id,'DELETE').then(function(data) {
          $scope.data.splice(index,1);
          getResultsPage($scope.pageNumber);
      });
    }
  }

  $scope.removeAll = function(){
    sessionStorage.clear();
    $scope.items = [];
    $.each(localStorage, (key) => {
      if ( !isNaN(key) && angular.isNumber(+key) || key == 0) {
        localStorage.removeItem(key);
      }
    });
    localStorage.removeItem("locked");
    $scope.searches = [];
    $scope.selectedItems = getData();
    $scope.selectedCount = getValue();
    $scope.totalPrice = getTotalPrice();
    $scope.transType = '';
    $scope.transNum  = '';
    reloadCheckout();
    $timeout(function () {        
        getResultsPage($scope.pageNumber);
    }, 100);
  }
   
  $scope.checkOUT = function()
  {       
    var $type    = $scope.form.type;
    var $payment = $scope.form.payment;
    var $stud_no = ($scope.searches.length != 0 && $scope.searches[0].searches == 'true') ? $scope.searches[0].stud_no : '';
    var json = [];

    angular.forEach($window.sessionStorage, function(i, v){
        json.push(i);
    });  

    $scope.pos = angular.copy(json); 

    if($type == 2 && $scope.searches[0].searches == 'false' && $scope.searches.length > 0) { 
    } else {
      swal({
          title: 'Are you sure?',
          text: 'the transaction will be served.',
          showCancelButton: true,
          confirmButtonText: 'Yes please!',
          cancelButtonText: 'No thanks!',
          confirmButtonColor: '#dc2430',
          cancelButtonColor: '#7b4397',
          showLoaderOnConfirm: true,
          preConfirm: function (isConfirm) {
              return new Promise(function (resolve, reject) {
                  if (isConfirm) {
                      $timeout(function () {
                          
                          console.log($scope.api + '/pos/create?user_id=' + $scope.profiles.user_id + '&type=' + $type + '&payment=' + $payment + '&barcode=' + $stud_no + '&trans_type=' + $scope.transType + '&trans_num=' + $scope.transNum);
                          dataFactory.httpRequest($scope.api + '/pos/create?user_id=' + $scope.profiles.user_id + '&type=' + $type + '&payment=' + $payment + '&barcode=' + $stud_no + '&trans_type=' + $scope.transType + '&trans_num=' + $scope.transNum,'POST',{},$scope.pos).then(function(data) {
                              $scope.data.push(data);
                              var total_payments = data.total_pay;
                              var trans_no = data.trans_no;

                              console.log($scope.api + '/customer/deduct-credit?barcode=' + $stud_no + '&total_payments=' + total_payments + "&trans_num=" + trans_no);
                              if($payment == 2) {
                                dataFactory.httpRequest($scope.api + '/customer/deduct-credit?barcode=' + $stud_no + '&total_payments=' + total_payments + "&trans_num=" + trans_no,'GET',{},'').then(function(datas) {
                                    console.log(datas);
                                });
                              }

                              $scope.dialog = data;
                              console.log(data);
                              if($scope.dialog.message) {
                                swal({
                                    title: $scope.dialog.header,
                                    text:  $scope.dialog.message,
                                    type:  $scope.dialog.type,
                                    timer: 2000,
                                    showConfirmButton: false
                                }).then(function(result) {
                                    if (result.dismiss === 'timer') {
                                        window.onkeydown = null;
                                        window.onfocus = null; 
                                        var hiddenText = angular.element('body input.hiddenText');
                                        hiddenText.focus();
                                        console.log('I was closed by the timer')
                                    }
                                });
                              }       
                          });

                          $scope.removeAll();
                          var modal_element = angular.element('.modal');
                          modal_element.modal('hide');         

                         resolve();
                      }, 1000);
                  }
              })
          },
          allowOutsideClick: false
          }).then(function (isConfirm) {     
              $timeout(function () {
              }, 100);
      },  function (dismiss) {
      }).catch(swal.noop); 
    }
  }

  $scope.forms = { type: 2, payment: 2 };
  $scope.resetForm = function() {
      angular.element('#keywords').val('');
      var selectpicker = angular.element('.selectpicker');
      selectpicker.selectpicker('val', '');
      selectpicker.selectpicker('refresh');
      $scope.form = angular.copy($scope.forms);
      $scope.searches = [];  
      $scope.balances = [];  
      reloadCheckout();    
  };
  $scope.resetForm();

  $scope.openCheckout = function() {
      if($scope.transType == 'reserved') {

          var json = [];
          angular.forEach($window.sessionStorage, function(i, v){
              json.push(i);
          });  
          $scope.pos = angular.copy(json); 

          swal({
          title: 'Are you sure?',
          text: 'the transaction (' + $scope.transNum + ') will be served.',
          showCancelButton: true,
          confirmButtonText: 'Yes please!',
          cancelButtonText: 'No thanks!',
          confirmButtonColor: '#dc2430',
          cancelButtonColor: '#7b4397',
          showLoaderOnConfirm: true,
          preConfirm: function (isConfirm) {
              return new Promise(function (resolve, reject) {
                  if (isConfirm) {
                      $timeout(function () {
                          
                          console.log($scope.api + '/pos/create?user_id=' + $scope.profiles.user_id + '&trans_type=' + $scope.transType + '&trans_num=' + $scope.transNum);
                          dataFactory.httpRequest($scope.api + '/pos/create?user_id=' + $scope.profiles.user_id + '&trans_type=' + $scope.transType + '&trans_num=' + $scope.transNum,'POST',{},$scope.pos).then(function(data) {
                              $scope.data.push(data);
                              $scope.dialog = data;
                              console.log(data);
                              if($scope.dialog.message) {
                                swal({
                                    title: $scope.dialog.header,
                                    text:  $scope.dialog.message,
                                    type:  $scope.dialog.type,
                                    timer: 2000,
                                    showConfirmButton: false
                                }).then(function(result) {
                                    if (result.dismiss === 'timer') {
                                        window.onkeydown = null;
                                        window.onfocus = null; 
                                        var hiddenText = angular.element('body input.hiddenText');
                                        hiddenText.focus();
                                        console.log('I was closed by the timer')
                                    }
                                });
                              }       
                          });

                          $scope.removeAll();
                          var modal_element = angular.element('.modal');
                          modal_element.modal('hide');         

                         resolve();
                      }, 1000);
                  }
              })
          },
          allowOutsideClick: false
          }).then(function (isConfirm) {     
              $timeout(function () {
                  $scope.selectedItems = getData();
                  $scope.selectedCount = getValue();
                  $scope.totalPrice = getTotalPrice();
              }, 100);
        },  function (dismiss) {
        }).catch(swal.noop);        
      } 
      else {
        if($scope.totalPrice != 0) { 
          $scope.resetForm();
          var modal = angular.element('#checkout-modal');
          modal.modal('show');
        }
      }
  }

  $scope.openAmountPaid = function() {
      var modal = angular.element('#edit-checkout-amount-modal');
      modal.modal('show');
      $scope.checkout_amounts = { totalPrice: (parseFloat($scope.totalPrice)- parseFloat($scope.totalDiscount)), totalPaid: $scope.totalPaid, totalChange: $scope.totalChange, totalDiscount: $scope.totalDiscount };
      $scope.checkout_amount = angular.copy($scope.checkout_amounts);
      $timeout(function () {
        var paid = angular.element('body #edit-checkout-amount-modal input[name="totalPaid"]');
        paid.focus();
      }, 500);
  }

  function reloadCheckout()
  { 
    var hiddenText = angular.element('body input.hiddenText');
    hiddenText.focus();
    $scope.totalPrice = getTotalPrice();
    $scope.totalPaid = parseFloat(0).toFixed(2);
    $scope.totalChange = ($scope.totalPaid > 0) ? parseFloat(parseFloat($scope.totalPaid) - parseFloat($scope.totalPrice)).toFixed(2) : parseFloat(0).toFixed(2);
    $scope.checkoutItems = $window.sessionStorage.length;
  }

  $scope.payment_mode = function() {
    if($scope.form.payment == 1) {
      $scope.totalPaid = parseFloat(0).toFixed(2);
      $scope.totalChange = parseFloat(0).toFixed(2);
      $scope.validate = false;
    } else {
      if($scope.searches.length != 0) {
        if($scope.searches[0].searches == 'true') {
          $scope.totalPaid = parseFloat($scope.searches[0].credits).toFixed(2);
          $scope.totalChange = ($scope.totalPaid > 0) ? parseFloat(parseFloat($scope.totalPaid) - parseFloat($scope.totalPrice)).toFixed(2) : parseFloat(0).toFixed(2);
          if(parseFloat(parseFloat($scope.totalPaid) + parseFloat($scope.totalDiscount)) >= parseFloat($scope.totalPrice)) {
            $scope.validate = true; 
          } else {
            $scope.validate = false;
          }
        } else {
          $scope.totalPaid = parseFloat(0).toFixed(2);
          $scope.totalChange = parseFloat(0).toFixed(2);
          $scope.validate = false;
        }
      } else {
        $scope.totalPaid = parseFloat(0).toFixed(2);
        $scope.totalChange = parseFloat(0).toFixed(2);
        $scope.validate = false;
      }
    }

  }

  $scope.clearKeywords = function(clear = '') {
    var keywords = angular.element('#keywords');
    $scope.form.keywords = '';
    $scope.searches = [];
    if(clear != '') {
      keywords.focus();
    }
  }

  $scope.searchKeywords = function() {
    var keywords = angular.element('#keywords').val();
    if(keywords.length > 0) {
      dataFactory.httpRequest($scope.sams_api + '/api/search_member_via_keywords/' + keywords).then(function(data) {
          console.log(data.info[0].searches);
          if(data.info[0].searches == 'true'){
            $scope.searches = data;
            console.log($scope.api + '/api/validate_transactions/' + keywords);
            dataFactory.httpRequest($scope.api + '/pos/validate_transactions/' + keywords).then(function(data2) {
                if(data2.transactions > 0) {
                  $scope.totalDiscount  = parseFloat(0).toFixed(2);
                } else {
                  $scope.totalDiscount  = parseFloat($scope.searches[0].discounts).toFixed(2);
                }
                console.log('discounts: ' + $scope.totalDiscount);
                if($scope.form.type == 2) {
                  $scope.totalPaid = parseFloat($scope.searches[0].credits).toFixed(2);
                  $scope.totalChange = ($scope.totalPaid > 0) ? parseFloat(parseFloat($scope.totalPaid) - parseFloat($scope.totalPrice)).toFixed(2) : parseFloat(0).toFixed(2);
                  
                  if(parseFloat($scope.totalPaid) >= parseFloat($scope.totalPrice)) {
                    $scope.validate = true; 
                  } else {
                    $scope.validate = false;
                  }
                }
            });

            // $scope.totalDiscount  = parseFloat($scope.searches[0].discounts).toFixed(2);
            // console.log('discounts: ' + $scope.totalDiscount);
            // if($scope.form.payment == 2) {
            //   $scope.totalPaid = parseFloat($scope.searches[0].credits).toFixed(2);
            //   $scope.totalChange = ($scope.totalPaid > 0) ? parseFloat(parseFloat($scope.totalPaid) - parseFloat($scope.totalPrice)).toFixed(2) : parseFloat(0).toFixed(2);
            //   if(parseFloat($scope.totalPaid) >= parseFloat($scope.totalPrice)) {
            //     $scope.validate = true; 
            //   } else {
            //     $scope.validate = false;
            //   }
            // }
          } else {
            $scope.searches = [];
            $scope.totalPaid  = parseFloat(0).toFixed(2);
            $scope.totalChange  = parseFloat(0).toFixed(2);
            $scope.totalDiscount  = parseFloat(0).toFixed(2);
            $scope.validate = false;
          }
      });
    }
    else
    {
      $scope.searches = [];
      $scope.totalPaid  = parseFloat(0).toFixed(2);
      $scope.totalChange  = parseFloat(0).toFixed(2);
      $scope.totalDiscount  = parseFloat(0).toFixed(2);
    }
  }

  $scope.concat_btn = function(element) {   
      if(element.currentTarget.value != 'clr') {
        if(element.currentTarget.value != '.'){
          $scope.checkout_item.quantity = $scope.checkout_item.quantity + "" + element.currentTarget.value;
        }else {
          if($scope.checkout_item.quantity.indexOf('.') === -1) {
            $scope.checkout_item.quantity = $scope.checkout_item.quantity + "" + element.currentTarget.value;
          }
        }
      } else {
        var str = $scope.checkout_item.quantity.toString();
        $scope.checkout_item.quantity = str.slice(0, -1);
      }

      if($scope.checkout_item.quantity != ''){
        $scope.checkout_item.total = parseFloat(parseFloat($scope.checkout_item.quantity) * parseFloat($scope.checkout_item.price)).toFixed(2);
      } else {
        $scope.checkout_item.total = parseFloat(0).toFixed(2);
      }
  }

  $scope.checkAmount = function() {
    var prices = angular.element('#edit-checkout-amount-modal #totalPrice');
    var paid = angular.element('#edit-checkout-amount-modal #totalPaid');
    var totals = (paid.val() > 0) ? parseFloat(paid.val()) - parseFloat(prices.val()) : 0; 
    $scope.checkout_amount.totalChange = parseFloat(totals).toFixed(2); 
    $scope.checkout_amount.totalPaid = paid.val();
  }

  $scope.editCheckoutAmount = function() {
    var modal = angular.element('#edit-checkout-amount-modal');
    modal.modal('hide');
    $scope.totalPaid = $scope.checkout_amount.totalPaid;
    $scope.totalChange = $scope.checkout_amount.totalChange;
    $scope.totalDiscount = $scope.checkout_amount.totalDiscount;
    if(parseFloat($scope.totalPaid) >= parseFloat(parseFloat($scope.totalPrice) - parseFloat($scope.totalDiscount)) ) {
      $scope.validate = true; 
    } else {
      $scope.validate = false;
    }
  }

  $scope.concat_amount_btn = function(element) {   
      if(element.currentTarget.value != 'clr') {
        if(element.currentTarget.value != '.'){
          if(!(parseFloat($scope.checkout_amount.totalPaid) > 0)) {
            if($scope.checkout_amount.totalPaid == '.') {
              $scope.checkout_amount.totalPaid = $scope.checkout_amount.totalPaid + "" + element.currentTarget.value;
            } else {
              $scope.checkout_amount.totalPaid = element.currentTarget.value;
            }
          } else {
            if($scope.checkout_amount.totalPaid.indexOf('.') === -1) {
              $scope.checkout_amount.totalPaid = $scope.checkout_amount.totalPaid + "" + element.currentTarget.value;
            } else {
              var str = $scope.checkout_amount.totalPaid.toString().split(".");
              if(str[1].length > 1) {
              } else {
                $scope.checkout_amount.totalPaid = $scope.checkout_amount.totalPaid + "" + element.currentTarget.value;
              }
            }
          }
        }else {
          if($scope.checkout_amount.totalPaid.indexOf('.') === -1) {
              if(!(parseFloat($scope.checkout_amount.totalPaid) > 0)) {
                $scope.checkout_amount.totalPaid = element.currentTarget.value;
              } else {
                $scope.checkout_amount.totalPaid = $scope.checkout_amount.totalPaid + "" + element.currentTarget.value;
              }
          } else {
            if(!(parseFloat($scope.checkout_amount.totalPaid) > 0)) {
              $scope.checkout_amount.totalPaid = element.currentTarget.value;
            }
          }
        }
      } else {
        if(!(parseFloat($scope.checkout_amount.totalPaid) > 0)) {
          $scope.checkout_amount.totalPaid = '';
        } else {
          var str = $scope.checkout_amount.totalPaid.toString();
          $scope.checkout_amount.totalPaid = str.slice(0, -1);
        }
      }

      if($scope.checkout_amount.totalPaid != '' && $scope.checkout_amount.totalPaid != '.'){
        $scope.checkout_amount.totalChange = parseFloat(parseFloat($scope.checkout_amount.totalPaid) - parseFloat($scope.checkout_amount.totalPrice)).toFixed(2);
      } else {
        $scope.checkout_amount.totalChange = parseFloat(0).toFixed(2);
      }
  }

  $scope.redirectLoad = function() {
      $window.location.href = '#/load-management';    
  }

  $scope.OpenBalance = function() {
      var modal = angular.element('#balance-modal');
      modal.modal('show');
  }

  $scope.clearBalance = function(clear = '') {
    var keywords = angular.element('#balance-modal input[name="keywords"]');
    $scope.form.keywords = '';
    $scope.searches = [];
    if(clear != '') {
      keywords.focus();
    }
  }

  $scope.searchBalance = function() {
    var keywords = angular.element('#balance-modal input[name="keywords"]').val();
    console.log($scope.sams_api + '/api/search_member_via_keywords/' + keywords);
    if(keywords.length > 0) {
      dataFactory.httpRequest($scope.sams_api + '/api/search_member_via_keywords/' + keywords).then(function(data) {
          console.log(data.info[0].searches);
          if(data.info[0].searches == 'true'){
            $scope.balances = data;
          } else {
            $scope.balances = [];
          }
      });
    }
    else
    {
      $scope.balances = [];
    }
  }

  $scope.proceedPendingTrans = function($transaction) {
      swal({
        title: 'Are you sure?',
        text: 'the transaction (' + $transaction + ') will be proceeded.',
        showCancelButton: true,
        confirmButtonText: 'Yes please!',
        cancelButtonText: 'No thanks!',
        confirmButtonColor: '#dc2430',
        cancelButtonColor: '#7b4397',
        showLoaderOnConfirm: true,
        preConfirm: function (isConfirm) {
            return new Promise(function (resolve, reject) {
                if (isConfirm) {
                    $timeout(function () {
                        
                        dataFactory.httpRequest($scope.api + '/pos/fetch-transaction/' + $transaction,'POST',{},$scope.pos).then(function(data) {
                            console.log(data);

                            if(data != '') {
                                sessionStorage.clear();
                                angular.forEach(data.items, function(group) {
                                    json = group; 
                                    if($window.sessionStorage.length == 0) {
                                      var indexs = 0;
                                    } else {
                                      var indexs = $window.sessionStorage.length;
                                    }
                                    $window.sessionStorage.setItem(indexs, JSON.stringify(json));       
                                }); 

                                $scope.transType = 'pending';
                                $scope.transNum  = data.infos.trans_no; 
                            }
                        }); 


                       resolve();
                    }, 1000);
                }
            })
        },
        allowOutsideClick: false
        }).then(function (isConfirm) {     
            $timeout(function () {
                $scope.selectedItems = getData();
                $scope.selectedCount = getValue();
                $scope.totalPrice = getTotalPrice();
            }, 100);
    },  function (dismiss) {
    }).catch(swal.noop);
  }

  $scope.cancelPendingTrans = function($transaction) {
      swal({
        title: 'Are you sure?',
        text: 'the transaction (' + $transaction + ') will be cancelled.',
        showCancelButton: true,
        confirmButtonText: 'Yes please!',
        cancelButtonText: 'No thanks!',
        confirmButtonColor: '#dc2430',
        cancelButtonColor: '#7b4397',
        showLoaderOnConfirm: true,
        preConfirm: function (isConfirm) {
            return new Promise(function (resolve, reject) {
                if (isConfirm) {
                    $timeout(function () {
                        
                        console.log($scope.api + '/pos/cancel-transaction/pending/' + $transaction + '?user_id=' + $scope.profiles.user_id);
                        dataFactory.httpRequest($scope.api + '/pos/cancel-transaction/pending/' + $transaction + '?user_id=' + $scope.profiles.user_id,'GET',{},$scope.pos).then(function(data) {
                            console.log(data);
                            $scope.dialog = data;
                            if($scope.dialog.message) {
                              swal({
                                  title: $scope.dialog.header,
                                  text:  $scope.dialog.message,
                                  type:  $scope.dialog.type,
                                  timer: 2000,
                                  showConfirmButton: false
                              }).then(function(result) {
                                  if (result.dismiss === 'timer') {
                                      window.onkeydown = null;
                                      window.onfocus = null; 
                                      var hiddenText = angular.element('body input.hiddenText');
                                      hiddenText.focus();
                                  }
                              });
                            }       
                        }); 

                       resolve();
                    }, 1000);
                }
            })
        },
        allowOutsideClick: false
        }).then(function (isConfirm) {     
            $timeout(function () {
            }, 100);
    },  function (dismiss) {
    }).catch(swal.noop);
  }

  $scope.proceedReservedTrans = function($transaction) {
      swal({
        title: 'Are you sure?',
        text: 'the transaction (' + $transaction + ') will be proceeded.',
        showCancelButton: true,
        confirmButtonText: 'Yes please!',
        cancelButtonText: 'No thanks!',
        confirmButtonColor: '#dc2430',
        cancelButtonColor: '#7b4397',
        showLoaderOnConfirm: true,
        preConfirm: function (isConfirm) {
            return new Promise(function (resolve, reject) {
                if (isConfirm) {
                    $timeout(function () {
                        
                        dataFactory.httpRequest($scope.api + '/pos/fetch-transaction/' + $transaction,'POST',{},$scope.pos).then(function(data) {
                            console.log(data);

                            if(data != '') {
                                sessionStorage.clear();
                                angular.forEach(data.items, function(group) {
                                    json = group; 
                                    if($window.sessionStorage.length == 0) {
                                      var indexs = 0;
                                    } else {
                                      var indexs = $window.sessionStorage.length;
                                    }
                                    $window.sessionStorage.setItem(indexs, JSON.stringify(json));       
                                }); 

                                $scope.transType = 'reserved';
                                $scope.transNum  = data.infos.trans_no; 
                            }
                        }); 


                       resolve();
                    }, 1000);
                }
            })
        },
        allowOutsideClick: false
        }).then(function (isConfirm) {     
            $timeout(function () {
                $scope.selectedItems = getData();
                $scope.selectedCount = getValue();
                $scope.totalPrice = getTotalPrice();
            }, 100);
      },  function (dismiss) {
      }).catch(swal.noop);
  }

  $scope.cancelReservedTrans = function($transaction) {
      swal({
        title: 'Are you sure?',
        text: 'the transaction (' + $transaction + ') will be cancelled.',
        showCancelButton: true,
        confirmButtonText: 'Yes please!',
        cancelButtonText: 'No thanks!',
        confirmButtonColor: '#dc2430',
        cancelButtonColor: '#7b4397',
        showLoaderOnConfirm: true,
        preConfirm: function (isConfirm) {
            return new Promise(function (resolve, reject) {
                if (isConfirm) {
                    $timeout(function () {
                        
                        dataFactory.httpRequest($scope.api + '/pos/cancel-transaction/reserved/' + $transaction + '?user_id=' + $scope.profiles.user_id,'GET',{},$scope.pos).then(function(data) {
                            console.log(data);
                            $scope.dialog = data;
                            if($scope.dialog.message) {
                              swal({
                                  title: $scope.dialog.header,
                                  text:  $scope.dialog.message,
                                  type:  $scope.dialog.type,
                                  timer: 2000,
                                  showConfirmButton: false
                              }).then(function(result) {
                                  if (result.dismiss === 'timer') {
                                      window.onkeydown = null;
                                      window.onfocus = null; 
                                      var hiddenText = angular.element('body input.hiddenText');
                                      hiddenText.focus();
                                  }
                              });
                            }       
                        }); 

                       resolve();
                    }, 1000);
                }
            })
        },
        allowOutsideClick: false
        }).then(function (isConfirm) {     
            $timeout(function () {
            }, 100);
    },  function (dismiss) {
    }).catch(swal.noop);
  }

  $scope.cancelServedTrans = function($transaction) {
      swal({
        title: 'Are you sure?',
        text: 'the transaction (' + $transaction + ') will be cancelled.',
        showCancelButton: true,
        confirmButtonText: 'Yes please!',
        cancelButtonText: 'No thanks!',
        confirmButtonColor: '#dc2430',
        cancelButtonColor: '#7b4397',
        showLoaderOnConfirm: true,
        preConfirm: function (isConfirm) {
            return new Promise(function (resolve, reject) {
                if (isConfirm) {
                    $timeout(function () {
                        
                        console.log($scope.api + '/pos/cancel-transaction/served/' + $transaction + '?user_id=' + $scope.profiles.user_id);
                        dataFactory.httpRequest($scope.api + '/pos/cancel-transaction/served/' + $transaction + '?user_id=' + $scope.profiles.user_id,'GET',{},$scope.pos).then(function(data) {
                            console.log(data);
                            $scope.dialog = data;
                            if($scope.dialog.message) {
                              swal({
                                  title: $scope.dialog.header,
                                  text:  $scope.dialog.message,
                                  type:  $scope.dialog.type,
                                  timer: 2000,
                                  showConfirmButton: false
                              }).then(function(result) {
                                  if (result.dismiss === 'timer') {
                                      window.onkeydown = null;
                                      window.onfocus = null; 
                                      var hiddenText = angular.element('body input.hiddenText');
                                      hiddenText.focus();
                                  }
                              });
                            }       
                        }); 

                       resolve();
                    }, 1000);
                }
            })
        },
        allowOutsideClick: false
        }).then(function (isConfirm) {     
            $timeout(function () {
            }, 100);
    },  function (dismiss) {
    }).catch(swal.noop);
  }

});

app.controller('NavCtrl', ['$scope', '$location', function($scope, $location) {
  $scope.getClass = function (path) {
    return ($location.path().substr(0, path.length) === path) ? 'm-menu__item--active' : '';
  }
}]);


app.controller('Settings_Controller', function(dataFactory, headerFooterData, headerFooterData2, $scope, $http,  $location, $routeParams, $window, $rootScope, $localStorage){

  $scope.segments = window.location.href.substr(window.location.href.lastIndexOf('/') + 1);
  $scope.itemPerPage = 5
  $scope.pageNumber = 1;
  $scope.libraryTemp = {};
  $scope.info = {};
  $scope.data = [];
  $scope.page = $routeParams.pagename;
  $scope.pagename = $routeParams.pagename.replace(/-/g, " ");
  $scope.innerContent = $routeParams.templateContent;  
  $scope.api = window.location.protocol + '//' + window.location.host + '/api';

  $scope.forms = { addDefault: 0 };
  $scope.resetForm = function() {
      var selectpicker = angular.element('.selectpicker');
      selectpicker.selectpicker('val', '');
      selectpicker.selectpicker('refresh');
      $scope.form = angular.copy($scope.forms);      
      angular.element().val('');
  };
  $scope.resetForm();

  angular.element(document).on('hidden.bs.modal', '.modal', function () {
      $scope.resetForm();
  });

  if($localStorage.loggedin == true) {
    headerFooterData2.getHeaderFooterData().then(function(data) {
      if(data.login === false) {
        $location.path('login').replace();
      } else {
        console.log(data);
        $scope.nav = data.menu;
      }
    });
  } else {
    headerFooterData.getHeaderFooterData().then(function(data) {
      if(data.login === false) {
        $location.path('login').replace();
      } else {
        console.log(data);
        $scope.nav = data.menu;
      }
    });
  }  

  getResultsPage($scope.pageNumber, '');
  function getResultsPage(pageNumber, search) {
      if(search != undefined && search != ''){
          dataFactory.httpRequest($scope.api + '/' + $scope.page + '/?search=' + search + '&page=' + pageNumber).then(function(data) {
              $scope.data = data.data;
              $scope.totalItems = data.total;
              $scope.pageNumber = pageNumber;
          });
      }else{
          dataFactory.httpRequest($scope.api + '/' + $scope.page + '/?page=' + pageNumber).then(function(data) {
              $scope.data = data.data;
              $scope.totalItems = data.total;
              $scope.pageNumber = pageNumber;
          });
      }
      console.log($scope.api + '/' + $scope.page + '/?search=' + search + '&page=' + pageNumber);
  }

  $scope.pageChanged = function(newPage) {
    $scope.pageNumber = newPage;
    getResultsPage($scope.pageNumber, $scope.info.searchText);
  };

  $scope.search = function(){
    getResultsPage(1, $scope.info.searchText);
  }

  $scope.remove = function(item,index){
    swal({
        title: 'Are you sure?',
        text: "You won't be able to revert this!",
        type: 'warning',
        showCancelButton: true,
        confirmButtonText: "Yes, remove it!",
    }).then(function(result) {
        if (result.value) {
            dataFactory.httpRequest($scope.api + '/' + $scope.page + '/remove/' + item, 'DELETE').then(function(data) {
              $scope.data.splice(index, 1);
              getResultsPage($scope.pageNumber);
            });
            swal(
                'Removed!',
                'The ' + $scope.pagename + ' has been successfully removed.',
                'success'
            );
        }
    });
  }

  $scope.edit = function(id){
    dataFactory.httpRequest($scope.api + '/' + $scope.page + '/edit/' + id).then(function(data) {
        console.log(data);
        $scope.form = data;
        angular.element(document).ready(function () { 
          $('select').selectpicker("render"); 
        });
    });
  }

  $scope.saveForm = function(){
    dataFactory.httpRequest($scope.api + '/' + $scope.page + '/create','POST',{},$scope.form).then(function(data) {
      $scope.data.push(data);
      $scope.info = data;
      console.log(data);
      if($scope.info.message) {
        swal(
            $scope.info.header,
            $scope.info.message,
            $scope.info.type
        );
      }
      var modal_element = angular.element('.modal');
      modal_element.modal('hide');
      getResultsPage(1, '');
    });
  }

  $scope.updateForm = function(){
    dataFactory.httpRequest($scope.api + '/' + $scope.page + '/update/' + $scope.form.id , 'POST', {}, $scope.form).then(function(data) {
      $scope.data.push(data);
      $scope.info = data;
      console.log(data);
      if($scope.info.message) {
        swal(
            $scope.info.header,
            $scope.info.message,
            $scope.info.type
        );
      }
      var modal_element = angular.element('.modal');
      modal_element.modal('hide');
      getResultsPage($scope.pageNumber);
    });
  }


  $scope.privileges = [ 
    {
      id: 1, text: 'Product Management', 
      crud: [
        {
          id: 'create_1', text: 'Create', label: 'create'
        },
        {
          id: 'read_1', text: 'Read', label: 'read'
        },
        {
          id: 'update_1', text: 'Update', label: 'update'
        },
        {
          id: 'delete_1', text: 'Delete', label: 'delete'
        }
      ]
    },
    {
      id: 2, text: 'System Settings', 
      crud: [
        {
          id: 'create_2', text: 'Create', label: 'create'
        },
        {
          id: 'read_2', text: 'Read', label: 'read'
        },
        {
          id: 'update_2', text: 'Update', label: 'update'
        },
        {
          id: 'delete_2', text: 'Delete', label: 'delete'
        }
      ]
    },
    {
      id: 3, text: 'Posting Inventory', 
      crud: [
        {
          id: 'create_3', text: 'Create', label: 'create'
        },
        {
          id: 'read_3', text: 'Read', label: 'read'
        },
        {
          id: 'update_3', text: 'Update', label: 'update'
        },
        {
          id: 'delete_3', text: 'Delete', label: 'delete'
        }
      ]
    },
    {
      id: 4, text: 'Generate Reports', 
      crud: [
        {
          id: 'create_4', text: 'Create', label: 'create'
        },
        {
          id: 'read_4', text: 'Read', label: 'read'
        },
        {
          id: 'update_4', text: 'Update', label: 'update'
        },
        {
          id: 'delete_4', text: 'Delete', label: 'delete'
        }
      ]
    },
    {
      id: 5, text: 'Supplier Management', 
      crud: [
        {
          id: 'create_5', text: 'Create', label: 'create'
        },
        {
          id: 'read_5', text: 'Read', label: 'read'
        },
        {
          id: 'update_5', text: 'Update', label: 'update'
        },
        {
          id: 'delete_5', text: 'Delete', label: 'delete'
        }
      ]
    },
    {
      id: 6, text: 'Customer Discount', 
      crud: [
        {
          id: 'create_6', text: 'Create', label: 'create'
        },
        {
          id: 'read_6', text: 'Read', label: 'read'
        },
        {
          id: 'update_6', text: 'Update', label: 'update'
        },
        {
          id: 'delete_6', text: 'Delete', label: 'delete'
        }
      ]
    },
    {
      id: 7, text: 'User Management', 
      crud: [
        {
          id: 'create_7', text: 'Create', label: 'create'
        },
        {
          id: 'read_7', text: 'Read', label: 'read'
        },
        {
          id: 'update_7', text: 'Update', label: 'update'
        },
        {
          id: 'delete_7', text: 'Delete', label: 'delete'
        }
      ]
    },
    {
      id: 8, text: 'Load Management', 
      crud: [
        {
          id: 'create_8', text: 'Create', label: 'create'
        },
        {
          id: 'read_8', text: 'Read', label: 'read'
        },
        {
          id: 'update_8', text: 'Update', label: 'update'
        },
        {
          id: 'delete_8', text: 'Delete', label: 'delete'
        }
      ]
    }
  ];


  // $scope.myData = [
  //   {
  //     "number": "2013-W45",
  //     "days": [
  //       {
  //         "dow": "1",
  //         "templateDay": "Monday",
  //         "jobs": [
  //           {
  //             "name": "Wakeup",
  //             "jobs": [
  //               {
  //                 "name": "prepare breakfast",

  //               }
  //             ]
  //           },
  //           {
  //             "name": "work 9-5",

  //           }
  //         ]
  //       },
  //       {
  //         "dow": "2",
  //         "templateDay": "Tuesday",
  //         "jobs": [
  //           {
  //             "name": "Wakeup",
  //             "jobs": [
  //               {
  //                 "name": "prepare breakfast",

  //               }
  //             ]
  //           }
  //         ]
  //       }
  //     ]
  //   }
  // ];

});

app.controller('Archived_Settings_Controller', function(dataFactory, headerFooterData, headerFooterData2, $scope, $http,  $location, $routeParams, $window, $rootScope, $localStorage){

  $scope.segments = window.location.href.substr(window.location.href.lastIndexOf('/') + 1);
  $scope.itemPerPage = 5
  $scope.pageNumber = 1;
  $scope.libraryTemp = {};
  $scope.info = {};
  $scope.data = [];
  $scope.page = $routeParams.pagename;
  $scope.pagename = $routeParams.pagename.replace(/-/g, " ");
  $scope.innerContent = $routeParams.templateContent;  
  $scope.api = window.location.protocol + '//' + window.location.host + '/api';

  if($localStorage.loggedin == true) {
    headerFooterData2.getHeaderFooterData().then(function(data) {
      if(data.login === false) {
        $location.path('login').replace();
      } else {
        console.log(data);
        $scope.nav = data.menu;
      }
    });
  } else {
    headerFooterData.getHeaderFooterData().then(function(data) {
      if(data.login === false) {
        $location.path('login').replace();
      } else {
        console.log(data);
        $scope.nav = data.menu;
      }
    });
  }  

  getResultsPage($scope.pageNumber, '');
  function getResultsPage(pageNumber, search) {
      if(search != undefined && search != ''){
          dataFactory.httpRequest($scope.api + '/' + $scope.page + '/archived?search=' + search + '&page=' + pageNumber).then(function(data) {
              $scope.data = data.data;
              $scope.totalItems = data.total;
              $scope.pageNumber = pageNumber;
          });
      }else{
          dataFactory.httpRequest($scope.api + '/' + $scope.page + '/archived?page=' + pageNumber).then(function(data) {
              $scope.data = data.data;
              $scope.totalItems = data.total;
              $scope.pageNumber = pageNumber;
          });
      }
      console.log($scope.api + '/' + $scope.page + '/archived?search=' + search + '&page=' + pageNumber);
  }

  $scope.pageChanged = function(newPage) {
    $scope.pageNumber = newPage;
    getResultsPage($scope.pageNumber, $scope.info.searchText);
  };

  $scope.search = function(){
    getResultsPage(1, $scope.info.searchText);
  }

  $scope.restore = function(id, index){
    swal({
        title: 'Are you sure?',
        text: "You won't be able to revert this!",
        type: 'warning',
        showCancelButton: true,
        confirmButtonText: "Yes, restore it!",
    }).then(function(result) {
        if (result.value) {
            dataFactory.httpRequest($scope.api + '/' + $scope.page + '/restore/' + id, 'POST').then(function(data) {
              $scope.data.splice(index, 1);
              getResultsPage($scope.pageNumber);
            });
            swal(
                'Restored!',
                'The ' + $scope.pagename + ' has been successfully restored.',
                'success'
            );
        }
    });
  }

});

app.controller('All_Settings_Controller', function(dataFactory, headerFooterData, headerFooterData2, $scope, $http,  $location, $routeParams, $window, $rootScope, $localStorage){

  $scope.segments = window.location.href.substr(window.location.href.lastIndexOf('/') + 1);
  $scope.itemPerPage = 5
  $scope.pageNumber = 1;
  $scope.libraryTemp = {};
  $scope.info = {};
  $scope.data = [];
  $scope.page = $routeParams.pagename;
  $scope.innerContent = $routeParams.templateContent;  
  $scope.api = window.location.protocol + '//' + window.location.host + '/api';

  if($localStorage.loggedin == true) {
    headerFooterData2.getHeaderFooterData().then(function(data) {
      if(data.login === false) {
        $location.path('login').replace();
      } else {
        console.log(data);
        $scope.nav = data.menu;
      }
    });
  } else {
    headerFooterData.getHeaderFooterData().then(function(data) {
      if(data.login === false) {
        $location.path('login').replace();
      } else {
        console.log(data);
        $scope.nav = data.menu;
      }
    });
  }  

  getResultsPage($scope.pageNumber, '');
  function getResultsPage(pageNumber, search) {
      if(search != undefined && search != ''){
          dataFactory.httpRequest($scope.api + '/settings?search=' + search + '&page=' + pageNumber).then(function(data) {
              $scope.data = data.data;
              $scope.totalItems = data.total;
              $scope.pageNumber = pageNumber;
          });
      }else{
          dataFactory.httpRequest($scope.api + '/settings?page=' + pageNumber).then(function(data) {
              $scope.data = data.data;
              $scope.totalItems = data.total;
              $scope.pageNumber = pageNumber;
          });
      }
  }

  $scope.pageChanged = function(newPage) {
    $scope.pageNumber = newPage;
    getResultsPage($scope.pageNumber, $scope.info.searchText);
  };

  $scope.search = function(){
    getResultsPage(1, $scope.info.searchText);
  }
  
});

app.controller('Users_Discount_Controller', function(dataFactory, $scope, $http, $location, $routeParams, $window, headerFooterData, headerFooterData2, $timeout, $rootScope, $localStorage){

  $scope.itemPerPage = 5
  $scope.pageNumber = 1;
  $scope.libraryTemp = {};
  $scope.datas = {};
  $scope.data = [];
  $scope.profiles = $localStorage.credentials;
  $scope.innerContent = $routeParams.templateContent;
  $scope.segments = window.location.href.substr(window.location.href.lastIndexOf('/') + 1);
  $scope.api = window.location.protocol + '//' + window.location.host + '/api';
  $scope.sams_api = window.location.protocol + '//' + window.location.host + '/samsv2';

  if($localStorage.loggedin == true) {
    headerFooterData2.getHeaderFooterData().then(function(data) {
      if(data.login === false) {
        $location.path('login').replace();
      } else {
        console.log(data);
        $scope.nav = data.menu;
      }
    });
  } else {
    headerFooterData.getHeaderFooterData().then(function(data) {
      if(data.login === false) {
        $location.path('login').replace();
      } else {
        console.log(data);
        $scope.nav = data.menu;
      }
    });
  }  

  
  $scope.user = {};
  $scope.resetForm = function() {
      var selectpicker = angular.element('.selectpicker');
      selectpicker.selectpicker('val', '');
      selectpicker.selectpicker('refresh');
      $scope.form = angular.copy($scope.user);
      $scope.form.members_name = '';
      $scope.form.members_no = '';
      get_all_users_discount('');
  };
  $scope.resetForm();

  angular.element(document).on('hidden.bs.modal', '.modal', function () {
      $scope.resetForm();
  });

  function get_all_users_discount($id) {     
      console.log($scope.sams_api + '/api/get_all_users_discount/' + $id);
      angular.element(document).ready(function () { 
          $http.get( $scope.sams_api + '/api/get_all_users_discount/' + $id )
            .then(function(data) 
            {
              console.log(data);
              $scope.mem_no = data.data;
          });
      });
  }

  getResultsPage(1);
  function getResultsPage(pageNumber, search) {
      if(search != undefined && search != ''){
          console.log($scope.sams_api + '/api/users_discount?search=' + search + '&page=' + pageNumber);
          dataFactory.httpRequest($scope.sams_api + '/api/users_discount?search=' + search + '&page=' + pageNumber).then(function(data) {
              $scope.data = data.data;
              $scope.totalItems = data.total;
              $scope.pageNumber = pageNumber;
          });
      }else{
          console.log($scope.sams_api + '/api/users_discount?page=' + pageNumber);
          dataFactory.httpRequest($scope.sams_api + '/api/users_discount?page=' + pageNumber).then(function(data) {
              $scope.data = data.data;
              $scope.totalItems = data.total;
              $scope.pageNumber = pageNumber;
          });
      }
  }

  $scope.searchMem = function($id){
    dataFactory.httpRequest($scope.sams_api + '/api/search_users_discount/' + $id).then(function(data) {
        $scope.user = { members_name: data.member, members_no: $id };
        $scope.form = angular.copy($scope.user);
    });
  }

  $scope.pageChanged = function(newPage) {
    getResultsPage(newPage, $scope.datas.searchText);
  };

  $scope.searchDB = function(){
    getResultsPage(1, $scope.datas.searchText);
  }

  $scope.edit = function(id){
    get_all_users_discount(id);
    var selectpicker = angular.element('.selectpicker');
    selectpicker.selectpicker('val', '');
    selectpicker.selectpicker('refresh');
    selectpicker.selectpicker('disabled');
    dataFactory.httpRequest($scope.sams_api + '/api/edit_users_discount/' + id).then(function(data) {
        console.log(data);
        $timeout(function () {
            $scope.form = data;
            angular.element(document).ready(function () { 
                $('select').selectpicker("render"); 
            });
        }, 100);
    });
  }

  $scope.saveForm = function(){
    dataFactory.httpRequest($scope.sams_api + '/api/create_users_discount?user_id=' + $scope.profiles.user_id,'POST',{},$scope.form).then(function(data) {
      $scope.data.push(data);
      $scope.info = data;
      console.log(data);
      if($scope.info.message) {
        swal(
            $scope.info.header,
            $scope.info.message,
            $scope.info.type
        );
      }
      var modal_element = angular.element('.modal');
      modal_element.modal('hide');
      getResultsPage(1, '');
    });
  }

  $scope.updateForm = function(){
    console.log($scope.form);
    dataFactory.httpRequest($scope.sams_api + '/api/update_users_discount/' + $scope.form.id + '?user_id='  + $scope.profiles.user_id , 'POST', {}, $scope.form).then(function(data) {
      $scope.data.push(data);
      $scope.info = data;
      console.log(data);
      if($scope.info.message) {
        swal(
            $scope.info.header,
            $scope.info.message,
            $scope.info.type
        );
      }
      var modal_element = angular.element('.modal');
      modal_element.modal('hide');
      getResultsPage($scope.pageNumber);
    });
  }

  $scope.remove = function(item,index){
    swal({
        title: 'Are you sure?',
        text: "You won't be able to revert this!",
        type: 'warning',
        showCancelButton: true,
        confirmButtonText: "Yes, remove it!",
    }).then(function(result) {
        if (result.value) {
            dataFactory.httpRequest($scope.api + '/users/remove/' + item, 'POST').then(function(data) {
              $scope.data.splice(index, 1);
              getResultsPage($scope.pageNumber);
            });
            swal(
                'Removed!',
                'The user has been successfully removed.',
                'success'
            );
        }
    });
  }

  

});

app.controller('Users_Controller', function(dataFactory, $scope, $http, $location, $routeParams, $window, headerFooterData, headerFooterData2, $timeout, $rootScope, $localStorage){

  $scope.itemPerPage = 5
  $scope.pageNumber = 1;
  $scope.libraryTemp = {};
  $scope.datas = {};
  $scope.data = [];
  $scope.innerContent = $routeParams.templateContent;
  $scope.segments = window.location.href.substr(window.location.href.lastIndexOf('/') + 1);
  $scope.api = window.location.protocol + '//' + window.location.host + '/api';

  if($localStorage.loggedin == true) {
    headerFooterData2.getHeaderFooterData().then(function(data) {
      if(data.login === false) {
        $location.path('login').replace();
      } else {
        console.log(data);
        $scope.nav = data.menu;
      }
    });
  } else {
    headerFooterData.getHeaderFooterData().then(function(data) {
      if(data.login === false) {
        $location.path('login').replace();
      } else {
        console.log(data);
        $scope.nav = data.menu;
      }
    });
  }  

  function get_all_profile_for_user() {
    console.log($scope.api + '/users/get-all-profile-for-user');
    dataFactory.httpRequest($scope.api + '/users/get-all-profile-for-user').then(function(data) {
        $scope.profiles = data;
    });
  }
  get_all_profile_for_user();

  function get_all_roles() {
    console.log($scope.api + '/users/get-all-roles');
    dataFactory.httpRequest($scope.api + '/users/get-all-roles').then(function(data) {
        $scope.roles = data;
    });
  }
  get_all_roles();

  function get_all_secret_question() {
    console.log($scope.api + '/users/get-all-secret-question');
    dataFactory.httpRequest($scope.api + '/users/get-all-secret-question').then(function(data) {
        $scope.secrets = data;
    });
  }
  get_all_secret_question();

  $scope.user = {};
  $scope.resetForm = function() {
      get_all_profile_for_user();
      var selectpicker = angular.element('.selectpicker');
      selectpicker.selectpicker('val', '');
      selectpicker.selectpicker('refresh');
      $scope.form = angular.copy($scope.user);
  };
  $scope.resetForm();

  angular.element(document).on('hidden.bs.modal', '.modal', function () {
      $scope.resetForm();
  });

  getResultsPage(1);
  function getResultsPage(pageNumber, search) {
      if(search != undefined && search != ''){
          dataFactory.httpRequest($scope.api + '/users?search=' + search + '&page=' + pageNumber).then(function(data) {
              $scope.data = data.data;
              $scope.totalItems = data.total;
              $scope.pageNumber = pageNumber;
          });
      }else{
          dataFactory.httpRequest($scope.api + '/users?page=' + pageNumber).then(function(data) {
              $scope.data = data.data;
              $scope.totalItems = data.total;
              $scope.pageNumber = pageNumber;
          });
      }
  }

  $scope.pageChanged = function(newPage) {
    getResultsPage(newPage, $scope.datas.searchText);
  };

  $scope.searchDB = function(){
    getResultsPage(1, $scope.datas.searchText);
  }

  $scope.edit = function(id){
    dataFactory.httpRequest($scope.api + '/users/edit/' + id).then(function(data) {
        console.log(data);
        $scope.profiles = data.profiles; 
        if($scope.profiles) {
          $timeout(function () {
              $scope.form = data.info;
              angular.element(document).ready(function () { 
                  $('select').selectpicker("render"); 
              });
          }, 100);
        }
    });
  }

  $scope.saveForm = function(){
    dataFactory.httpRequest($scope.api + '/users/create','POST',{},$scope.form).then(function(data) {
      $scope.data.push(data);
      $scope.info = data;
      console.log(data);
      if($scope.info.message) {
        swal(
            $scope.info.header,
            $scope.info.message,
            $scope.info.type
        );
      }
      var modal_element = angular.element('.modal');
      modal_element.modal('hide');
      getResultsPage(1, '');
    });
  }

  $scope.updateForm = function(){
    console.log($scope.form);
    dataFactory.httpRequest($scope.api + '/users/update/' + $scope.form.id , 'POST', {}, $scope.form).then(function(data) {
      $scope.data.push(data);
      $scope.info = data;
      console.log(data);
      if($scope.info.message) {
        swal(
            $scope.info.header,
            $scope.info.message,
            $scope.info.type
        );
      }
      var modal_element = angular.element('.modal');
      modal_element.modal('hide');
      getResultsPage($scope.pageNumber);
    });
  }

  $scope.remove = function(item,index){
    swal({
        title: 'Are you sure?',
        text: "You won't be able to revert this!",
        type: 'warning',
        showCancelButton: true,
        confirmButtonText: "Yes, remove it!",
    }).then(function(result) {
        if (result.value) {
            dataFactory.httpRequest($scope.api + '/users/remove/' + item, 'POST').then(function(data) {
              $scope.data.splice(index, 1);
              getResultsPage($scope.pageNumber);
            });
            swal(
                'Removed!',
                'The user has been successfully removed.',
                'success'
            );
        }
    });
  }

});

app.controller('Archived_Users_Controller', function(dataFactory, $scope, $http, $location, $routeParams, $window, headerFooterData, headerFooterData2, $timeout, $rootScope, $localStorage){

  $scope.itemPerPage = 5
  $scope.pageNumber = 1;
  $scope.libraryTemp = {};
  $scope.datas = {};
  $scope.data = [];
  $scope.innerContent = $routeParams.templateContent;
  $scope.segments = window.location.href.substr(window.location.href.lastIndexOf('/') + 1);
  $scope.api = window.location.protocol + '//' + window.location.host + '/api';

  if($localStorage.loggedin == true) {
    headerFooterData2.getHeaderFooterData().then(function(data) {
      if(data.login === false) {
        $location.path('login').replace();
      } else {
        console.log(data);
        $scope.nav = data.menu;
      }
    });
  } else {
    headerFooterData.getHeaderFooterData().then(function(data) {
      if(data.login === false) {
        $location.path('login').replace();
      } else {
        console.log(data);
        $scope.nav = data.menu;
      }
    });
  }  

  getResultsPage(1);
  function getResultsPage(pageNumber, search) {
      if(search != undefined && search != ''){
          dataFactory.httpRequest($scope.api + '/users/archived?search=' + search + '&page=' + pageNumber).then(function(data) {
              $scope.data = data.data;
              $scope.totalItems = data.total;
              $scope.pageNumber = pageNumber;
          });
      }else{
          dataFactory.httpRequest($scope.api + '/users/archived?page=' + pageNumber).then(function(data) {
              $scope.data = data.data;
              $scope.totalItems = data.total;
              $scope.pageNumber = pageNumber;
          });
      }
  }

  $scope.pageChanged = function(newPage) {
    getResultsPage(newPage, $scope.datas.searchText);
  };

  $scope.searchDB = function(){
    getResultsPage(1, $scope.datas.searchText);
  }

  $scope.restore = function(item,index){
    swal({
        title: 'Are you sure?',
        text: "You won't be able to revert this!",
        type: 'warning',
        showCancelButton: true,
        confirmButtonText: "Yes, restore it!",
    }).then(function(result) {
        if (result.value) {
            dataFactory.httpRequest($scope.api + '/users/restore/' + item, 'POST').then(function(data) {
              $scope.data.splice(index, 1);
              getResultsPage($scope.pageNumber);
            });
            swal(
                'Removed!',
                'The user has been successfully restored.',
                'success'
            );
        }
    });
  }

});

app.controller('LoadController', function(dataFactory, $scope, $http, $location, $routeParams, $window, headerFooterData, headerFooterData2, $timeout, $rootScope, $localStorage){

  angular.element(document).ready(function () { 
      var modal = angular.element('body .modal-backdrop').remove();
  });

  $scope.itemPerPage = 5
  $scope.pageNumber = 1;
  $scope.libraryTemp = {};
  $scope.datas = {};
  $scope.data = [];
  $scope.profiles = $localStorage.credentials;
  $scope.innerContent = $routeParams.templateContent;
  $scope.api = window.location.protocol + '//' + window.location.host + '/api';
  $scope.sams_api = window.location.protocol + '//' + window.location.host + '/samsv2';

  if($localStorage.loggedin == true) {
    headerFooterData2.getHeaderFooterData().then(function(data) {
      if(data.login === false) {
        $location.path('login').replace();
      } else {
        console.log(data);
        $scope.nav = data.menu;
      }
    });
  } else {
    headerFooterData.getHeaderFooterData().then(function(data) {
      if(data.login === false) {
        $location.path('login').replace();
      } else {
        console.log(data);
        $scope.nav = data.menu;
      }
    });
  }  

  $scope.item = {};
  $scope.resetForm = function() {
      var selectpicker = angular.element('.selectpicker');
      selectpicker.selectpicker('val', '');
      selectpicker.selectpicker('refresh');
      $scope.form = angular.copy($scope.item);
  };
  $scope.resetForm();

  angular.element(document).on('hidden.bs.modal', '.modal', function () {
      $scope.resetForm();
  });

  getResultsPage(1);
  function getResultsPage(pageNumber, search) {
    console.log(search);
      if(search != undefined && search != ''){
          dataFactory.httpRequest($scope.sams_api + '/api?search=' + search + '&page=' + pageNumber).then(function(data) {
              $scope.data = data.data;
              $scope.totalItems = data.total;
              $scope.pageNumber = pageNumber;
              console.log($scope.sams_api + '/api?search=' + search + '&page=' + pageNumber);
          });
      }else{
          dataFactory.httpRequest($scope.sams_api + '/api?page=' + pageNumber).then(function(data) {
              $scope.data = data.data;
              $scope.totalItems = data.total;
              $scope.pageNumber = pageNumber;
              console.log($scope.sams_api + '/api?page=' + pageNumber);
          });
      }
  }

  $scope.pageChanged = function(newPage) {
      getResultsPage(newPage, $scope.datas.searchText);
  };

  $scope.searchDB = function(){
      getResultsPage(1, $scope.datas.searchText);
  }

  $scope.parseDate = function($date) {
    return $date;
  }

  $scope.parseAmount = function($amount) {
    return price_separator(parseFloat($amount).toFixed(2));
  }

  function price_separator (input) {
      var output = input
      if (parseFloat(input)) {
          input = new String(input); // so you can perform string operations
          var parts = input.split("."); // remove the decimal part
          parts[0] = parts[0].split("").reverse().join("").replace(/(\d{3})(?!$)/g, "$1,").split("").reverse().join("");
          output = parts.join(".");
      }

      return output;
  }

  $scope.saveForm = function(){
    var $id = angular.element('#add-load-modal #idz').val();
    var $credits = angular.element('#add-load-modal #credits').val();
    console.log($scope.sams_api + '/api/create/' + $id + '?credits=' + $credits, 'POST',{}, $scope.form);
    swal({
        title: 'Are you sure?',
        text: 'this member will be loaded.',
        showCancelButton: true,
        confirmButtonText: 'Yes please!',
        cancelButtonText: 'No thanks!',
        confirmButtonColor: '#dc2430',
        cancelButtonColor: '#7b4397',
        showLoaderOnConfirm: true,
        preConfirm: function (isConfirm) {
            return new Promise(function (resolve, reject) {
                if (isConfirm) {
                    $timeout(function () {
                        dataFactory.httpRequest($scope.sams_api + '/api/create/' + $id + '?credits=' + $credits, 'POST',{}, $scope.form).then(function(data) {
                          $scope.data.push(data);
                          $scope.info = data;
                          console.log(data);
                          if($scope.info.message) {
                            swal(
                                $scope.info.header,
                                $scope.info.message,
                                $scope.info.type
                            );
                          }                 

                          var varStudno    = data.datas.stud_no;
                          var varCredit    = data.datas.credit;
                          var varTimestamp = data.datas.timestamp;
                          console.log($scope.api + '/load/store?user_id=' + $scope.profiles.user_id + '&stud_no=' + varStudno + '&credit=' + varCredit + '&timestamp=' + varTimestamp);
                          dataFactory.httpRequest($scope.api + '/load/store?user_id=' + $scope.profiles.user_id + '&stud_no=' + varStudno + '&credit=' + varCredit + '&timestamp=' + varTimestamp, 'GET',{}, $scope.form).then(function(data) {
                              console.log(data);
                          });

                        });
                        var modal_element = angular.element('.modal');
                        modal_element.modal('hide');
                        
                       resolve();
                    }, 2000);
                }
            })
        },
        allowOutsideClick: false
        }).then(function (isConfirm) {     
            $timeout(function () {
                getResultsPage(1, '');
            }, 100);
    },  function (dismiss) {
    }).catch(swal.noop);    
  }

  $scope.addLoad = function($id, $stud_no, $firstname, $middlename, $lastname, $msisdn, $level, $credit){
      console.log($id, $stud_no, $firstname, $middlename, $lastname, $msisdn, $level, $credit);
      var modal = angular.element('#add-load-modal');
      modal.find('.fullname').text($firstname + ' ' + $middlename + ' ' + $lastname);
      modal.find('.msisdn').text($msisdn);
      modal.find('.level').text($level);
      modal.find('.stud_no').text($stud_no);
      modal.find('.idz').val($id);
      modal.find('.credit').text(parseFloat($credit).toFixed(2));
  }

});

app.controller('ReportsController', function(dataFactory, headerFooterData, headerFooterData2, $scope, $http,  $location, $routeParams, $window, $rootScope, $localStorage, $timeout){

  var params = $location.search();
  $scope.category = params.category;

  $scope.libraryTemp = {};
  $scope.data = [];
  $scope.profiles = $localStorage.credentials;
  $scope.innerContent = $routeParams.templateContent;  
  $scope.api = window.location.protocol + '//' + window.location.host + '/api';
  
  $scope.formData      = {};
  $scope.formData.date = "";
  $scope.opened        = false;
  $scope.ReportsData = [];
  $scope.totalAmount = 0;
  $scope.ReportsItems = '';

  $scope.categories = [
    { name: 'Sales Summary', value: '1' }, 
    { name: 'Detailed Sales', value: '2' }, 
    { name: 'Load Credit', value: '3' }
  ];
  
  $scope.orders = [
    { name: 'A-z', value: 'ASC' }, 
    { name: 'z-A', value: 'DESC' }
  ];

  $scope.reports = {};
  $scope.resetForm = function() {
      var selectpicker = angular.element('.selectpicker');
      selectpicker.selectpicker('val', '');
      selectpicker.selectpicker('refresh');
      $timeout(function(){
        $('.selectpicker').selectpicker('refresh'); 
        $('#m_datepicker_1, #m_datepicker_1_validate').datepicker({
            todayHighlight: true,
            orientation: "bottom left",
            templates: {
                leftArrow: '<i class="la la-angle-left"></i>',
                rightArrow: '<i class="la la-angle-right"></i>'
            }
        });
      },100)
      $scope.form = angular.copy($scope.reports);      
  };
  $scope.resetForm();

  if($localStorage.loggedin == true) {
    headerFooterData2.getHeaderFooterData().then(function(data) {
      if(data.login === false) {
        $location.path('login').replace();
      } else {
        console.log(data);
        $scope.nav = data.menu;
      }
    });
  } else {
    headerFooterData.getHeaderFooterData().then(function(data) {
      if(data.login === false) {
        $location.path('login').replace();
      } else {
        console.log(data);
        $scope.nav = data.menu;
      }
    });
  }
  
  $scope.parseAmount = function($amount) {
    return price_separator(parseFloat($amount).toFixed(2));
  }

  $scope.totalAmounts = function($amount, $quantity) {
    return price_separator( parseFloat( parseFloat($amount) * parseFloat($quantity) ).toFixed(2));
  }

  $scope.totalNetAmount = function($amount, $quantity, $discount) {
    return price_separator( parseFloat( (parseFloat($amount) * parseFloat($quantity)) - parseFloat($discount) ).toFixed(2));
  }

  $scope.generate = function() {

      var date1 = $scope.form.date_from;
      var date2 = $scope.form.date_to;
      var url = 'date_from=' + date1.replace(/\//g, '-')  + '&date_to=' + date2.replace(/\//g, '-') + '&category=' + $scope.form.category + '&order=' + $scope.form.order;

      $window.location.href = '#/generate-reports/view?' + url;

  }

  $scope.dates = function() {
      var params = $location.search();
      return 'As of date ' + params.date_from + ' to ' + params.date_to;
  }

  generate();
  function generate() {
      var params = $location.search();
      var date_from = params.date_from;
      var date_to   = params.date_to;
      var category  = params.category;
      var order     = params.order;

      console.log($scope.api + '/pos/generate-transactions?date_from=' + date_from + '&date_to=' + date_to + '&category=' + category + '&order=' + order);
      dataFactory.httpRequest($scope.api + '/pos/generate-transactions?date_from=' + date_from + '&date_to=' + date_to + '&category=' + category + '&order=' + order).then(function(data) {
        $scope.ReportsData = data.data;
        $scope.totalAmount = data.total_amount;
        console.log($scope.ReportsData);
      });
  }

  function price_separator (input) {
      var output = input
      if (parseFloat(input)) {
          input = new String(input); // so you can perform string operations
          var parts = input.split("."); // remove the decimal part
          parts[0] = parts[0].split("").reverse().join("").replace(/(\d{3})(?!$)/g, "$1,").split("").reverse().join("");
          output = parts.join(".");
      }

      return output;
  }

  function fixed_url(str) {
    return encodeURIComponent(str).replace(/[!'()*]/g, function(c) {
      return '%' + c.charCodeAt(0).toString(16);
    });
  }

  function GetURLParameter(sParam) {
      var sPageURL = window.location.search.substring(1);
      var sURLVariables = sPageURL.split('&');
      for (var i = 0; i < sURLVariables.length; i++) 
      {
          var sParameterName = sURLVariables[i].split('=');
          if (sParameterName[0] == sParam) 
          {
              return sParameterName[1];
          }
      }
  }

  $scope.prints = function(w,h) {
    var dualScreenLeft = window.screenLeft != undefined ? window.screenLeft : window.screenX;
    var dualScreenTop = window.screenTop != undefined ? window.screenTop : window.screenY;

    var width = window.innerWidth ? window.innerWidth : document.documentElement.clientWidth ? document.documentElement.clientWidth : screen.width;
    var height = window.innerHeight ? window.innerHeight : document.documentElement.clientHeight ? document.documentElement.clientHeight : screen.height;

    var left = ((width / 2) - (w / 2)) + dualScreenLeft;
    var top = ((height / 2) - (h / 2)) + dualScreenTop;
    
    var printContents = document.getElementById('print-layout').innerHTML;
    var popupWin = window.open('', '_blank', 'width='+w+',height='+h+',top='+top+',left='+left+'scrollbars=no,menubar=no,toolbar=no,location=no,status=no,titlebar=no');
    popupWin.window.focus();
    popupWin.document.open();
    popupWin.document.write('<!DOCTYPE html><html><head><title>GENERATED REPORTS</title>' 
    +'<link href="assets/vendors/base/vendors.bundle.css" rel="stylesheet" type="text/css" />'
    +'<link href="assets/default/base/style.bundle.css" rel="stylesheet" type="text/css" />'  
    +'<link href="bower_components/perfect-scrollbar/css/perfect-scrollbar.css" rel="stylesheet" type="text/css" />'
    +'<link href="bower-components/angular-ui-bootstrap/dist/ui-bootstrap-csp.css" rel="stylesheet" type="text/css" />'
    +'<link href="assets/css/style.css" rel="stylesheet" type="text/css" />'
    +'</head><body onload="window.print(); window.close();"><div>' 
    + printContents + '</div></html>');
    popupWin.document.close();
  }


});

app.filter('capitalize', function() {
    return function(input) {
      return (!!input) ? input.charAt(0).toUpperCase() + input.substr(1).toLowerCase() : '';
    }
});


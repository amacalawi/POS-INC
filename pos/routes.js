var app =  angular.module('main-App',['ngRoute','angularUtils.directives.dirPagination','ui.bootstrap','ngStorage']);

app.config(['$routeProvider',
    function($routeProvider) {
        $routeProvider.
            when('/manage-system', {
                templateUrl: 'partials/templates/layouts.html',
                controller: 'ManageController',
                resolve: {
                    templateContent: function ($route) { $route.current.params.templateContent = 'partials/view/manage-system.html'; }
                }
            })
            .when('/login', {
                templateUrl: 'partials/templates/guest.html',
                controller: 'Login_Controller',
                resolve: {
                    templateContent: function ($route) { $route.current.params.templateContent = 'partials/view/login.html'; }
                }
            })
            .when('/logout', {
                templateUrl: 'partials/templates/guest.html',
                controller: 'Logout_Controller',
                resolve: {
                    templateContent: function ($route) { $route.current.params.templateContent = 'partials/view/login.html'; }
                }
            })
            .when('/supplier-management', {
                templateUrl: 'partials/templates/layouts.html',
                controller: 'SuppliersController',
                resolve: {
                    templateContent: function ($route) { $route.current.params.templateContent = 'partials/view/suppliers.html'; }
                }
            })
            .when('/supplier-management/manage', {
                templateUrl: 'partials/templates/layouts.html',
                controller: 'SuppliersController',
                resolve: {
                    templateContent: function ($route) { $route.current.params.templateContent = 'partials/view/suppliers.html'; }
                }
            })
            .when('/supplier-management/archived', {
                templateUrl: 'partials/templates/layouts.html',
                controller: 'ArchivedSuppliersController',
                resolve: {
                    templateContent: function ($route) { $route.current.params.templateContent = 'partials/view/archived-suppliers.html'; }
                }
            })
            .when('/supplier-management/:pagename', {
                templateUrl: 'partials/templates/layouts.html',
                controller: 'SuppliersController',
                resolve: {
                    templateContent: function ($route) { $route.current.params.templateContent = 'partials/view/suppliers.html'; }
                }
            })
            .when('/gl-accounts', {
                templateUrl: 'partials/templates/layouts.html',
                controller: 'GL_AccountsController',
                resolve: {
                    templateContent: function ($route) { $route.current.params.templateContent = 'partials/view/gl-accounts.html'; }
                }
            })
            .when('/gl-accounts/manage', {
                templateUrl: 'partials/templates/layouts.html',
                controller: 'GL_AccountsController',
                resolve: {
                    templateContent: function ($route) { $route.current.params.templateContent = 'partials/view/gl-accounts.html'; }
                }
            })
            .when('/gl-accounts/archived', {
                templateUrl: 'partials/templates/layouts.html',
                controller: 'Archived_GL_AccountsController',
                resolve: {
                    templateContent: function ($route) { $route.current.params.templateContent = 'partials/view/archived-gl-accounts.html'; }
                }
            })
            .when('/gl-accounts/:pagename', {
                templateUrl: 'partials/templates/layouts.html',
                controller: 'GL_AccountsController',
                resolve: {
                    templateContent: function ($route) { $route.current.params.templateContent = 'partials/view/gl-accounts.html'; }
                }
            })
            .when('/settings', {
                templateUrl: 'partials/templates/layouts.html',
                controller: 'All_Settings_Controller',
                resolve: {
                    templateContent: function ($route) { $route.current.params.templateContent = 'partials/manage/manage-settings.html'; }
                }
            })
            .when('/settings/:pagename/manage', {
                templateUrl: 'partials/templates/layouts.html',
                controller: 'Settings_Controller',
                resolve: {
                    templateContent: function ($route) { $route.current.params.templateContent = 'partials/manage/settings.html'; }
                }
            })
            .when('/settings/:pagename/archived', {
                templateUrl: 'partials/templates/layouts.html',
                controller: 'Archived_Settings_Controller',
                resolve: {
                    templateContent: function ($route) { $route.current.params.templateContent = 'partials/archived/settings.html'; }
                }
            })
            .when('/generate-reports', {
                templateUrl: 'partials/templates/layouts.html',
                controller: 'ReportsController',
                resolve: {
                    templateContent: function ($route) { $route.current.params.templateContent = 'partials/manage/generate-reports.html'; }
                }
            })  
            .when('/generate-reports/:pagename', {
                templateUrl: 'partials/templates/layouts.html',
                controller: 'ReportsController',
                resolve: {
                    templateContent: function ($route) { $route.current.params.templateContent = 'partials/view/generate-reports.html'; }
                }
            })  
            .when('/load-management', {
                templateUrl: 'partials/templates/layouts.html',
                controller: 'LoadController',
                resolve: {
                    templateContent: function ($route) { $route.current.params.templateContent = 'partials/manage/load-management.html'; }
                }
            })   
            .when('/user-management/manage', {
                templateUrl: 'partials/templates/layouts.html',
                controller: 'Users_Controller',
                resolve: {
                    templateContent: function ($route) { $route.current.params.templateContent = 'partials/manage/users.html'; }
                }
            })
            .when('/user-management/archived', {
                templateUrl: 'partials/templates/layouts.html',
                controller: 'Archived_Users_Controller',
                resolve: {
                    templateContent: function ($route) { $route.current.params.templateContent = 'partials/archived/users.html'; }
                }
            })
            .when('/user-discount/manage', {
                templateUrl: 'partials/templates/layouts.html',
                controller: 'Users_Discount_Controller',
                resolve: {
                    templateContent: function ($route) { $route.current.params.templateContent = 'partials/manage/users-discount.html'; }
                }
            })
            .when('/user-discount/archived', {
                templateUrl: 'partials/templates/layouts.html',
                controller: 'Archived_Users_Discount_Controller',
                resolve: {
                    templateContent: function ($route) { $route.current.params.templateContent = 'partials/archived/users-discounts.html'; }
                }
            })
            .when('/posting-inventory', {
                templateUrl: 'partials/templates/layouts.html',
                controller: 'Posting_Inventory_Controller',
                resolve: {
                    templateContent: function ($route) { $route.current.params.templateContent = 'partials/manage/posting-inventory.html'; }
                }
            })
            .when('/posting-inventory/manage', {
                templateUrl: 'partials/templates/layouts.html',
                controller: 'Posting_Inventory_Controller',
                resolve: {
                    templateContent: function ($route) { $route.current.params.templateContent = 'partials/manage/posting-inventory.html'; }
                }
            })
            .when('/posting-inventory/archived', {
                templateUrl: 'partials/templates/layouts.html',
                controller: 'Archived_Posting_Inventory_Controller',
                resolve: {
                    templateContent: function ($route) { $route.current.params.templateContent = 'partials/archived/posting-inventory.html'; }
                }
            })
            .when('/posting-inventory/:pagename', {
                templateUrl: 'partials/templates/layouts.html',
                controller: 'Posting_Inventory_Controller',
                resolve: {
                    templateContent: function ($route) { $route.current.params.templateContent = 'partials/view/posting-inventory.html'; }
                }
            })
        	.when('/product-management', {
                templateUrl: 'partials/templates/layouts.html',
                controller: 'ProductController',
                resolve: {
                    templateContent: function ($route) { $route.current.params.templateContent = 'partials/manage/product-management.html'; }
                }
            })            
            .when('/product-management/manage', {
                templateUrl: 'partials/templates/layouts.html',
                controller: 'ProductController',
                resolve: {
                    templateContent: function ($route) { $route.current.params.templateContent = 'partials/manage/product-management.html'; }
                }
            })
            .when('/product-management/archived', {
                templateUrl: 'partials/templates/layouts.html',
                controller: 'ArchivedProductController',
                resolve: {
                    templateContent: function ($route) { $route.current.params.templateContent = 'partials/archived/product-management.html'; }
                }
            })
            .when('/product-management/:pagename', {
                templateUrl: 'partials/templates/layouts.html',
                controller: 'Item_Controller',
                resolve: {
                    templateContent: function ($route) { $route.current.params.templateContent = 'partials/manage/products.html'; }
                }
            })
            .when('/product-management/:pagename/manage', {
                templateUrl: 'partials/templates/layouts.html',
                controller: 'Item_Controller',
                resolve: {
                    templateContent: function ($route) { $route.current.params.templateContent = 'partials/manage/products.html'; }
                }
            })
            .when('/product-management/:pagename/archived', {
                templateUrl: 'partials/templates/layouts.html',
                controller: 'Item_Archived_Controller',
                resolve: {
                    templateContent: function ($route) { $route.current.params.templateContent = 'partials/archived/products.html'; }
                }
            })
            .when('/kiosk/:pagename', {
                templateUrl: 'partials/templates/kiosk.html',
                controller: 'KioskController',
                resolve: {
                    templateContent: function ($route) { $route.current.params.templateContent = 'partials/view/kiosk.html'; }
                }
            })
            .when('/:pagename', {
                templateUrl: 'partials/templates/layouts.html',
                controller: 'POSController',
                resolve: {
                    templateContent: function ($route) { $route.current.params.templateContent = 'partials/view/pos.html'; }
                }
            })
            .otherwise({
                redirectTo: '/meals',
            });
        }
    ]
);
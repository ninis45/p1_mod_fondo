(function () {
    'use strict';
    
     angular.module('app.fondo')
     .controller('IndexCtrl',['$scope','$http','$cookies',IndexCtrl])
     .controller('InputCtrl',['$scope','$http',InputCtrl]);
     
     function IndexCtrl($scope,$http,$cookies)
     {
        
         
        $scope.oneAtATime =  true;
        $scope.open_mes = 2;//parseInt($cookies.get('fondo:mes'));
        
        //console.log($scope.open_mes);
        $scope.get_mes = function(index)
        {
            if($scope.open_mes == index)
                return true;
            return false;
        }
        $scope.set_mes = function(index)
        {
            
            //$scope.active = index;
            //$scope.open_mes = index;
            $cookies.put('fondo:mes',index);
        }
        $scope.select = function()
        {
            $scope.open_mes=0;
            console.log($scope.open_mes);
        }
     }
     function InputCtrl($scope,$http)
     {
        
        
        $scope.f_actividad={};
        $scope.f_concepto={};
        $scope.f_partida={};
        $scope.f_centro={};
        $scope.f_director={};
        $scope.fecha_ini=new Date();
        $scope.fecha_fin=new Date();
        $scope.hide_shortcuts=true;
        $scope.isCollapsed=true;
        $scope.f_autorizado = 1;
        
        $scope.hide= {pdf:true,xml:true};
        
        
        
        
        /****datepicker****/
        $scope.openCalendar = function($event) {
            $scope.status.opened = true;
        };
        
        $scope.dateOptions = {
            formatYear: 'yy',
            startingDay: 1
        };
        $scope.remove_file = function(slug)
        {
            
        }
        
        
        
       $scope.$watch('f_autorizado',function(newValue,oldValue){
         console.log(newValue);
        
        });
        
        $scope.$watch('f_actividad.selected',function(newValue,oldValue){
            
            
            
             if(!newValue) return ;
             
             if(oldValue && oldValue != newValue )
             {
                $scope.f_concepto={id:''};
             }
             
                $http.post(SITE_URL+'admin/fondo/list_concepto',{id_actividad_poa:newValue,anio:$scope.anio}).then(function(response){
                
                   
                   
                   $scope.f_conceptos=response.data;
                   
                });
            
                
            
            
        });
        
        $scope.$watch('f_concepto.selected',function(newValue,oldValue){
            
            
            //console.log(newValue);
            if(!newValue) return ;
             
            if(oldValue && oldValue != newValue )$scope.f_partida={id:''};
             
            
            
                $http.post(SITE_URL+'admin/fondo/list_partida',{id_actividad_poa:$scope.f_actividad.selected,id_concepto:newValue.id}).then(function(response){
                
                   
                   
                    $scope.f_partidas=response.data;
                    
                   
                    
                   
                });
            
                
            
            
        });
        
        $scope.$watch('f_centro.selected',function(newValue,oldValue){
            
            var id_centro = newValue;
           
            if(!newValue)
            {
               return ;
            }
            
            
            if(oldValue && oldValue != newValue )$scope.f_director='';
            
            $http.post(SITE_URL+'admin/fondo/list_directores',{id_centro:id_centro}).then(function(response){
                
                $scope.f_directores = response.data;
                
            });
           
            
            
            
        });
        
       
        /*if($scope.f_centro.selected)
        {
            $scope.get_directores() ;   
        }
        
        $scope.get_directores = function()
        {
            
            $http.post(SITE_URL+'admin/fondo/list_directores',{id_centro:$scope.f_centro.selected}).then(function(response){
                
                $scope.directores = response.data;
                
            });
        }*/
        
        
        
     }
     
})(); 
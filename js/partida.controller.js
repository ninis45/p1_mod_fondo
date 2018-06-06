(function () {
    'use strict';
    
     angular.module('app.partidas')
     .controller('InputCtrl',['$scope','$http',InputCtrl]);
     
     function InputCtrl($scope,$http)
     {
         //$scope.f_conceptos=[];
         //$scope.f_actividad={};
         //$scope.anio='';
         
         $scope.$watch('f_actividad.selected',function(newValue,oldValue){
            console.log(newValue);
            console.log(oldValue);
           
            if(!newValue) return ;
            
            if(newValue != oldValue) $scope.f_concepto ='';
            
            var data={
                
                id_actividad_poa: newValue,
                anio:$scope.anio
                
            };
            $http.post(SITE_URL+'admin/fondo/list_concepto',data).then(function(response){
                $scope.f_conceptos = angular.fromJson(response.data);
                
            });
         });
     }
 
     
})(); 
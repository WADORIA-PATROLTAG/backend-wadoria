var reponseTraitementData="";
var dataConnexion        ="";
var dataBons             ="";
var numBons              ="";
var numBonsIncrement     ="entree";
var typeValue            ="";
var alldatasBons         ="";

function traitementData(datas){
    for(var i=0; i<datas.length; i++)
    {
        if($("#"+datas[i]).val()==""){
            swal("Veuillez remplir le (la) "+datas[i],"Erreur remplissage", "error");
            return false;
        }else if(i==datas.length-1){
            reponseTraitementData="nickel";
        }else{
            
        }
    }   
}
/*
*Modele standard de transmission de data vers le front-end
*En utilisant du ajax
*/
function querySend(datas,domain){
    var query={};
        query.url  =domain;
        query.data  =datas;
        query.type  ="POST";
        query.success=function(accuseReception){ 
          console.log(accuseReception);
            reponse         =accuseReception.reponse.status;
            typeReponse     =accuseReception.reponse.type;
            message         =accuseReception.reponse.message;
            dataRecu        =accuseReception.reponse.data;
            dataRecu2       =accuseReception.reponse.datas;
            otp             =accuseReception.reponse.otp;
            smail           =accuseReception.reponse.email;
           
            if(reponse=="success"){
                if(typeReponse=="reservation"){
                    noloader();
                    swal(message,typeReponse,'success');
                }else if(typeReponse=="bons"){
                            numBons =dataRecu;
                            dataBons=dataRecu2;
                }else if(typeReponse=="Enregistrement"){
                    swal(message,typeReponse,'success');
                    dataConnexion=dataRecu;
                    creationLocalstorageUsers(dataRecu);
                   
                }else{
                     swal(message);
                }
            }else{
                swal(message,typeReponse,'error');
            }
        }
        query.error=function(erreur){
            swal("Une erreur est survenue veuillez contacterl'administrateur pour en savoir plus", "Erreur Application code 001", "error");
            console.log(erreur);
        }
        $.ajax(query);
}


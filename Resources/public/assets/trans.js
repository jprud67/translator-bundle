(function($){
    let url=$("#translation_container");

    $(".trans-save").click(function (e) {
        e.preventDefault();
        let content_el=$(this).next("textarea");
        $.ajax({
            url : url.attr("data-trans-save-path"),
            data:  {
                field: content_el.attr("data-trans-field"),
                locale: content_el.attr("data-trans-locale"),
                class: content_el.attr("data-trans-class"),
                foreignKey: content_el.attr("data-trans-foreignKey"),
                content: content_el.val(),
            },
            type : 'POST',
            dataType : 'json',
            success : function(code_json, statut){
                Swal.fire({
                    type: 'success',
                    title: code_json,
                })
            },
            error : function(resultat, statut, erreur){
            },
            complete : function(resultat, statut){
            }
        });

    });

    $.ajax({
        url : url.attr("data-trans-path"),
        type : 'GET',
        dataType : 'json',
        success : function(data, statut){
            let translations=data.trans;
            for (let trans of translations ) {
                let objectClass=trans.objectClass;
                let  className=objectClass.split("\\");
                className=className[className.length-1];
                $("#"+className+trans.field+trans.foreignKey+trans.locale).val(trans.content)
            }
        },
        error : function(resultat, statut, erreur){
        },
        complete : function(resultat, statut){
        }
    });


})(jQuery);
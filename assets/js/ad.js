
$('#add-image').on("click",function()
        {
            // Je récupère le numéro des futurs champs que je vais créer
            const index = +$('#widgets-counter').val();
  
            // je récupère le prototype des entrées
            const tmpl = $('#idee_images').data('prototype').replace(/__name__/g, index);

            //J'injecte ce code au sein de la div
            $('#idee_images').append(tmpl);
            $('#widgets-counter').val(index + 1);


            // Je gère le bouton supprimer
            handleDeleteButtons();

        });

        function handleDeleteButtons()
        {
            $('button[data-action="delete"]').on("click",function()
            {
                const target = this.dataset.target;

                $(target).remove();
            });
        }

        function updateCounter()
        {
            const count = +$('#idee_images div.form-group').length;

            $('#widgets-counter').val(count);
        }
        updateCounter();
        handleDeleteButtons();


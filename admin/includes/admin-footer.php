            </div>
        </main>
    </div>

    <script>
        if (document.querySelector('.tinymce-editor')) {
            tinymce.init({
                selector: '.tinymce-editor',
                plugins: 'lists link image code',
                toolbar: 'undo redo | bold italic underline | bullist numlist | link image | code',
                menubar: false,
                height: 300,
                language: 'fr_FR',

                /* Désactiver la conversion des URLs en chemins relatifs */
                relative_urls: false,
                remove_script_host: false,
                convert_urls: false,

                /* upload images - utiliser file_picker pour remplacer la popup par défaut */
                automatic_uploads: true,
                file_picker_types: 'image',
                file_picker_callback: function(callback, value, meta) {
                    if (meta.filetype === 'image') {
                        var input = document.createElement('input');
                        input.setAttribute('type', 'file');
                        input.setAttribute('accept', 'image/*');

                        input.onchange = function() {
                            var file = this.files[0];
                            var formData = new FormData();
                            formData.append('file', file);

                            fetch('ajax/upload_image_tinymce.php', {
                                method: 'POST',
                                body: formData
                            })
                            .then(response => response.json())
                            .then(result => {
                                if (result.location) {
                                    callback(result.location, { title: file.name });
                                } else {
                                    alert('Erreur: ' + (result.error || 'Upload échoué'));
                                }
                            })
                            .catch(error => {
                                alert('Erreur lors de l\'upload');
                            });
                        };

                        input.click();
                    }
                },

                content_style: 'body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif; font-size: 14px; }'
            });
        }
    </script>
</body>
</html>

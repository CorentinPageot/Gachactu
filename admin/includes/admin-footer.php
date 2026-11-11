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

                relative_urls: false,
                remove_script_host: false,
                convert_urls: false,

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
                            .catch(() => {
                                alert('Erreur lors de l\'upload');
                            });

                        };

                        input.click();
                    }
                },

                content_style: 'body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif; font-size: 14px; }'
            });
        }


        // Gestion affichage champs intégrations actualités

        function toggleActualiteIntegrations() {

            const lootbarCheckbox = document.getElementById('lootbar_enabled');
            const igCheckbox = document.getElementById('ig_enabled');

            const lootbarFields = document.getElementById('lootbar_fields');
            const igFields = document.getElementById('ig_fields');

            if (lootbarCheckbox && lootbarFields) {

                lootbarFields.style.display = lootbarCheckbox.checked ? 'block' : 'none';

                lootbarCheckbox.addEventListener('change', function() {
                    lootbarFields.style.display = this.checked ? 'block' : 'none';
                });

            }

            if (igCheckbox && igFields) {

                igFields.style.display = igCheckbox.checked ? 'block' : 'none';

                igCheckbox.addEventListener('change', function() {
                    igFields.style.display = this.checked ? 'block' : 'none';
                });

            }
        }


        document.addEventListener('DOMContentLoaded', toggleActualiteIntegrations);

    </script>
</body>
</html>

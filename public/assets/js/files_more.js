function addButtonFuncs(files_div_id, button_id, code) {
        files_div = document.querySelector(files_div_id)
        document.getElementById(button_id).addEventListener('click', function() {
            files_div.insertAdjacentHTML('beforeend', code)
        })
    }

    document.addEventListener('click', function(e) {
        lect_files_div = document.querySelector('#files_div')
        prak_files_div = document.querySelector('#files_div_prak')
        lect_alert = document.querySelector('#lect_max')
        prak_alert = document.querySelector('#prak_max')

        if (e.target.id === 'add_file') {
            if (lect_files_div.childNodes.length < 15) {
                lect_files_div.insertAdjacentHTML('beforeend', `
            <div class="input-group">
                <label class="input-group__label">Добавить лекционный файл</label>
                <input type="file" class="input-group__input" name="lesson_lect[]">
            </div>
        `);
            } else {
                if (lect_alert == undefined) {
                    lect_files_div.insertAdjacentHTML('beforeend', `
                    <label class="input-group__label" style="color:red" id="lect_max">Максимум 5 файлов</label>
                `);
                }
            }
        }

        if (e.target.id === 'add_file_prak') {
            if(prak_files_div.childNodes.length < 15) {
                prak_files_div.insertAdjacentHTML('beforeend', `
            <div class="input-group">
                <label class="input-group__label">Добавить практический файл</label>
                <input type="file" class="input-group__input" name="lesson_prak[]">
            </div>
        `);
            }
            else {
                if (prak_alert == undefined) {
                    prak_files_div.insertAdjacentHTML('beforeend', `
                    <label class="input-group__label" style="color:red" id="prak_max">Максимум 5 файлов</label>
                `);
                }
            }
        }
    });
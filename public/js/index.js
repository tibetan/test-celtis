'use strict';

let data = '<div class="msgNotice">Вие сте влезли в страницата за първи път и за това данните все още не са получени</div>';
setImagesInfoToPage(data);

function handleClick() {
    let chosenDate = document.getElementById('picker-date').value;
    if (chosenDate.length > 0) {
        fetchImagesInfo(chosenDate);
    } else {
        data = '<div class="msgNotice">Моля, изберете дата</div>';
        setImagesInfoToPage(data);
    }
}

function fetchImagesInfo(date) {
    fetch('api/request.php', {
        method: 'POST',
        mode: 'cors',
        credentials: 'include',
        body: JSON.stringify({
            action: 'fetchImagesInfo',
            payload: {
                chosenDate: date,
            }
        })
    })
        .then(function (response) {
            return response.json();
        })
        .then(json => {
            if (json.type !== 0) {
                data = '<div class="msgFatal">Грешка при извличане на данни: ' + json.message + '</div>';
            } else {
                const resp = json.data;
                const respLength = resp.length;
                if (respLength > 0) {
                    let tbody = '';
                    for (let i = 0; i < respLength; i++) {
                        tbody += '<tr><td>' + resp[i].url + '</td><td>' + resp[i].count + '</td><td>' + resp[i].date + '</td></tr>';
                        console.log(resp[i].count);
                    }

                    data = '<table class="info-results">\n' +
                    '    <thead>\n' +
                    '    <tr>\n' +
                    '        <th>Url</th>\n' +
                    '        <th>Брой картинки</th>\n' +
                    '        <th>Дата</th>\n' +
                    '    </tr>\n' +
                    '    </thead>\n' +
                    '    <tbody>' + tbody + '</tbody>\n' +
                    '</table>';
                } else {
                    data = '<div class="msgNotice">Няма данни!</div>';
                }
            }

            setImagesInfoToPage(data);
        })
        .catch(error => {
            debugger;
            console.log('unknown error ', error);
        });
}

function setImagesInfoToPage(data) {
    document.getElementById('dataBlock').innerHTML = data;
}

function isObjectEmpty(obj) {
    for (let key in obj) {
        if (obj.hasOwnProperty(key)) {
            return false;
        }
    }
    return true;
}

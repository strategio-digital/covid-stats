/**
 * Copyright (c) 2021 Strategio Digital s.r.o.
 * @author Jiří Zapletal (https://strategio.digital, jz@strategio.digital)
 */

[].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
    .map(element => new bootstrap.Tooltip(element));

const ddM = new bootstrap.Modal(document.getElementById('death-detail-modal'));
const ddMBody = document.getElementById('death-detail-modal-body');

[].slice.call(document.querySelectorAll('[data-btn="death-detail"]'))
    .map((element) => element.addEventListener('click', () =>
        axios.get('/api/death-detail?date=' + element.dataset.date).then((response) => {
            ddMBody.innerHTML = response.data;
            ddM.show();
        })));
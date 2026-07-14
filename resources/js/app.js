import Alpine from 'alpinejs';

window.Alpine = Alpine;

Alpine.start();

document.addEventListener('DOMContentLoaded', () => {
    const reportPage = window.location.pathname.match(
        /^\/manager\/reports\/(revenue|bookings|passengers|occupancy|airline-performance|route-performance)\/?$/
    );

    if (!reportPage) {
        return;
    }

    const reportUrl = window.location.pathname.replace(/\/$/, '');
    const pdfButton = document.querySelector('button .fa-file-pdf')?.closest('button');
    const excelButton = document.querySelector('button .fa-file-excel')?.closest('button');

    if (pdfButton) {
        pdfButton.removeAttribute('onclick');
        pdfButton.addEventListener('click', () => {
            window.location.href = `${reportUrl}/export/pdf`;
        });
    }

    if (excelButton) {
        excelButton.addEventListener('click', () => {
            window.location.href = `${reportUrl}/export/excel`;
        });
    }
});

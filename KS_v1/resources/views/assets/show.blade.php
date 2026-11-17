@extends('assets.asset-layout')
 

@section('asset_section')
 
   
   @include('assets.section.'.$tab)

@endsection

@section('page-script')

<script>
document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.delete-form').forEach(form => {
        form.addEventListener('submit', function (e) {
            e.preventDefault();

            Swal.fire({
                title: 'Are you sure?',
                text: "This action cannot be undone!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Yes, delete it!',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        });
    });

    const downloadBtn = document.getElementById('downloadAssetQrBtn');
    if (!downloadBtn) {
        return;
    }

    downloadBtn.addEventListener('click', function () {
        const assetCode = @json($asset->asset_code ?? '');
        const svgEl = document.querySelector('#asset-qr-download-source svg');

        if (!svgEl || !assetCode) {
            return;
        }

        const svgData = new XMLSerializer().serializeToString(svgEl);
        const svgBlob = new Blob([svgData], { type: 'image/svg+xml;charset=utf-8' });
        const url = URL.createObjectURL(svgBlob);
        const img = new Image();

        img.onload = function () {
            const padding = 40;
            const textHeight = 56;
            const canvas = document.createElement('canvas');
            canvas.width = img.width + (padding * 2);
            canvas.height = img.height + (padding * 2) + textHeight;
            const ctx = canvas.getContext('2d');

            ctx.fillStyle = '#ffffff';
            ctx.fillRect(0, 0, canvas.width, canvas.height);
            ctx.drawImage(img, padding, padding);

            ctx.fillStyle = '#000000';
            ctx.font = 'bold 32px Arial, sans-serif';
            ctx.textAlign = 'center';
            ctx.textBaseline = 'top';
            ctx.fillText(assetCode, canvas.width / 2, img.height + padding + 12);

            canvas.toBlob(function (blob) {
                if (!blob) {
                    return;
                }

                const link = document.createElement('a');
                link.download = assetCode + '-qrcode.png';
                link.href = URL.createObjectURL(blob);
                link.click();
                URL.revokeObjectURL(link.href);
            }, 'image/png');

            URL.revokeObjectURL(url);
        };

        img.onerror = function () {
            URL.revokeObjectURL(url);
        };

        img.src = url;
    });
});
</script>

@endsection

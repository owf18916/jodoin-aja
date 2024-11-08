<script>
    function confirmDelete(id, event) {
        Swal.fire({
            title: 'Yakin ingin menghapus data?',
            text: 'Data yang dihapus tidak bisa dikembalikan lagi',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Ya!'
        }).then((result) => {
            if (result.isConfirmed) {
                Livewire.emit(event, id);
            }
        });
    }
</script>

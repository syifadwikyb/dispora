</main>
<footer class="text-center mt-5 py-3 bg-light">
    <p>&copy; <?= date('Y'); ?> Sistem Manajemen Surat</p>
</footer>
<script src="/ams/assets/js/bootstrap.bundle.min.js"></script>
<script>
        function confirmDelete(deleteUrl) {
            Swal.fire({
                title: 'Apakah Anda yakin?',
                text: "Data yang dihapus tidak akan bisa dikembalikan!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya, hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {                
                    window.location.href = deleteUrl;
                }
            })
        }
        </script>
</body>

</html>
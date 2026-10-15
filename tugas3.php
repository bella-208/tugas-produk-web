

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Satamart</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
</head>
<body>
  <nav class="navbar navbar-dark bg-dark px-3">
    <a class="navbar-brand fs-6" href="#">Satamart</a>
  </nav>

  <div class="container-fluid">
    <div class="row">

      <aside class="col-md-2 sidebar p-3">
        <nav class="nav flex-column py-2">

          <a href="#" class="nav-link active" data-page="Dashboard">
            <i class="bi bi-house-door me-2"></i>Dashboard
          </a>
          <a href="#" class="nav-link" data-page="Orders">
            <i class="bi bi-receipt me-2"></i>Orders
          </a>
          <a href="#" class="nav-link" data-page="Products">
            <i class="bi bi-cart me-2"></i>Products
          </a>
          <a href="#" class="nav-link" data-page="Customers">
            <i class="bi bi-people me-2"></i>Customers
          </a>
          <a href="#" class="nav-link" data-page="Reports">
            <i class="bi bi-graph-up me-2"></i>Reports
          </a>
          <a href="#" class="nav-link" data-page="Integrations">
            <i class="bi bi-gear me-2"></i>Integrations
          </a>

          <hr class="mx-3">

          <a href="#" class="nav-link" id="signOut">
            <i class="bi bi-box-arrow-left me-2"></i>Sign out
          </a>
        </nav>
      </aside>

      <main class="col-md-10 main-content p-4">

        <h2 class="h3 pb-2 border-bottom" id="judul">Dashboard</h2>

        <div id="halamanProduk">
          <h3 class="h4 mt-3">Data Produk</h3>
          <button class="btn btn-secondary btn-sm mb-2" id="btnTambah">
            Add Product
          </button>

          <div class="table-responsive">
            <table class="table table-striped table-sm align-middle">
              <thead>
                <tr>
                  <th>No</th>
                  <th>Nama Barang</th>
                  <th>Harga</th>
                  <th>Stok</th>
                  <th>Aksi</th>
                </tr>
              </thead>
              <tbody id="tbody"></tbody>
            </table>
          </div>
        </div>

        <p id="halamanLain" class="text-muted d-none">
          Halaman ini belum tersedia.
        </p>
      </main>
    </div>
  </div>

  <!-- Modal tambah / edit -->
  <div class="modal fade" id="modalProduk" tabindex="-1">
    <div class="modal-dialog">
      <form class="modal-content" id="formProduk">
        <div class="modal-header">
          <h5 class="modal-title" id="modalJudul">Add Product</h5>
          <button type="button" class="btn-close"
                  data-bs-dismiss="modal" aria-label="Close"></button>
        </div>

        <div class="modal-body">
          <div class="mb-3">
            <label class="form-label" for="fNama">Nama Barang</label>
            <input type="text" class="form-control" id="fNama" required>
          </div>

          <div class="mb-3">
            <label class="form-label" for="fHarga">Harga</label>
            <input type="number" class="form-control" id="fHarga"
                   min="0" step="1" required>
          </div>

          <div class="mb-3">
            <label class="form-label" for="fStok">Stok</label>
            <input type="number" class="form-control" id="fStok"
                   min="0" step="1" required>
          </div>
        </div>

        <div class="modal-footer">
          <button type="button" class="btn btn-outline-secondary btn-sm"
                  data-bs-dismiss="modal">Batal</button>
          <button type="submit" class="btn btn-secondary btn-sm">
            Simpan
          </button>
        </div>
      </form>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

  <script>
    const KEY = 'satamart_produk';

    let produk;

    try {
      const data = JSON.parse(localStorage.getItem(KEY));
      produk = Array.isArray(data) ? data : [];
    } catch (e) {
      produk = [];
    }

    const simpan = () => {
      try {
        localStorage.setItem(KEY, JSON.stringify(produk));
        return true;
      } catch (e) {
        alert('Gagal menyimpan data produk.');
        return false;
      }
    };

    const esc = s => String(s).replace(/[&<>"']/g, c => ({
      '&': '&amp;',
      '<': '&lt;',
      '>': '&gt;',
      '"': '&quot;',
      "'": '&#39;'
    })[c]);

    const rp = n => 'Rp ' + Number(n).toLocaleString('id-ID');

    const modal = new bootstrap.Modal(
      document.getElementById('modalProduk')
    );

    let editId = null;

    function render() {
      document.getElementById('tbody').innerHTML = produk.length
        ? produk.map((p, i) => `
          <tr>
            <td>${i + 1}</td>
            <td>${esc(p.nama)}</td>
            <td>${rp(p.harga)}</td>
            <td>${p.stok}</td>
            <td>
              <a href="#" class="text-dark me-2"
                 data-edit="${p.id}">Edit</a>
              <a href="#" class="text-dark"
                 data-hapus="${p.id}">Hapus</a>
            </td>
          </tr>
        `).join('')
        : '<tr><td colspan="5" class="text-muted">Belum ada produk.</td></tr>';
    }

    // Tambah produk
    document.getElementById('btnTambah').addEventListener('click', () => {
      editId = null;
      document.getElementById('modalJudul').textContent = 'Add Product';
      document.getElementById('formProduk').reset();
      modal.show();
    });

    // Edit dan hapus produk
    document.getElementById('tbody').addEventListener('click', e => {
      const edit = e.target.closest('[data-edit]');
      const hapus = e.target.closest('[data-hapus]');

      if (edit) {
        e.preventDefault();

        const p = produk.find(x => x.id === Number(edit.dataset.edit));
        if (!p) {
          alert('Produk tidak ditemukan.');
          return;
        }

        editId = p.id;

        document.getElementById('modalJudul').textContent = 'Edit Product';
        document.getElementById('fNama').value = p.nama;
        document.getElementById('fHarga').value = p.harga;
        document.getElementById('fStok').value = p.stok;

        modal.show();
      }

      if (hapus) {
        e.preventDefault();

        const p = produk.find(x => x.id === Number(hapus.dataset.hapus));
        if (!p) {
          alert('Produk tidak ditemukan.');
          return;
        }

        if (confirm('Hapus "' + p.nama + '"?')) {
          const dataLama = produk;
          produk = produk.filter(x => x.id !== p.id);

          if (simpan()) {
            render();
          } else {
            produk = dataLama;
          }
        }
      }
    });

    // Simpan produk baru atau perubahan
    document.getElementById('formProduk').addEventListener('submit', e => {
      e.preventDefault();

      const data = {
        nama: document.getElementById('fNama').value.trim(),
        harga: Number(document.getElementById('fHarga').value),
        stok: Number(document.getElementById('fStok').value)
      };

      if (!data.nama) {
        alert('Nama barang wajib diisi.');
        return;
      }

      if (!Number.isSafeInteger(data.harga) || data.harga < 0) {
        alert('Harga harus berupa bilangan bulat minimal 0.');
        return;
      }

      if (!Number.isSafeInteger(data.stok) || data.stok < 0) {
        alert('Stok harus berupa bilangan bulat minimal 0.');
        return;
      }

      const dataLama = produk.map(p => ({ ...p }));

      if (editId !== null) {
        const index = produk.findIndex(p => p.id === editId);

        if (index === -1) {
          alert('Produk yang diedit tidak ditemukan.');
          return;
        }

        produk[index] = { ...produk[index], ...data };
      } else {
        const idBaru = produk.reduce(
          (maks, p) => Math.max(maks, Number(p.id) || 0), 0
        ) + 1;

        produk.push({ id: idBaru, ...data });
      }

      if (simpan()) {
        render();
        modal.hide();
        document.getElementById('formProduk').reset();
        editId = null;
      } else {
        produk = dataLama;
      }
    });

    // Menu sidebar
    document.querySelectorAll('[data-page]').forEach(a => {
      a.addEventListener('click', e => {
        e.preventDefault();

        const halaman = a.dataset.page;
        document.getElementById('judul').textContent = halaman;

        const adaProduk =
          halaman === 'Dashboard' || halaman === 'Products';

        document.getElementById('halamanProduk')
          .classList.toggle('d-none', !adaProduk);

        document.getElementById('halamanLain')
          .classList.toggle('d-none', adaProduk);

        document.querySelectorAll('[data-page]').forEach(link => {
          link.classList.toggle('active', link === a);
        });
      });
    });

    // Sign out
    document.getElementById('signOut').addEventListener('click', e => {
      e.preventDefault();

      if (confirm('Yakin ingin keluar?')) {
        alert('Kamu sudah keluar (demo).');
      }
    });

    render();
  </script>
</body>
</html>
<?php
// Aside menu
return [

    'items' => [
        // Dashboard
        [
            'title' => 'Dashboard',
            'root' => true,
            'icon' => 'media/svg/icons/Design/Layers.svg', // or can be 'flaticon-home' or any flaticon-*
            'page' => '/',
            'new-tab' => false,
        ],

        //Pembelian        
        [
            'title' => 'Pembelian',
            'icon' => 'media/svg/icons/Layout/Layout-4-blocks.svg',
            'bullet' => 'line',
            'root' => true,
            'submenu' => [
                [
                    'title' => 'Daftar Pembelian',
                    'bullet' => 'dot',
                    'page' => '/',
                    'new-tab' => false,
                ],
                [
                    'title' => 'Penerimaan Barang',
                    'bullet' => 'dot',
                    'page' => '/',
                    'new-tab' => false,
                ],
                [
                    'title' => 'Faktur Pembelian',
                    'bullet' => 'dot',
                    'page' => '/',
                    'new-tab' => false,
                ],
            ]
        ],

        //Penjualan
        [
            'title' => 'Penjualan',
            'icon' => 'media/svg/icons/Shopping/Barcode-read.svg',
            'bullet' => 'dot',
            'root' => true,
            'submenu' => [
                [
                    'title' => 'Penawaran',
                    'bullet' => 'dot',
                    'page' => '/',
                    'new-tab' => false,
                ],
                [
                    'title' => 'Daftar Penjualan',
                    'bullet' => 'dot',
                    'page' => '/',
                    'new-tab' => false,
                ],
                [
                    'title' => 'Pengiriman Barang',
                    'bullet' => 'dot',
                    'page' => '/',
                    'new-tab' => false,
                ],
                [
                    'title' => 'Faktur Penjualan',
                    'bullet' => 'dot',
                    'page' => '/',
                    'new-tab' => false,
                ],
                [
                    'title' => 'Tukar Faktur',
                    'bullet' => 'dot',
                    'page' => '/',
                    'new-tab' => false,
                ],
            ]
        ],

        //Keuangan Accounting
        [
            'title' => 'Keuangan / Accounting',
            'icon' => 'media/svg/icons/Shopping/Barcode-read.svg',
            'bullet' => 'dot',
            'root' => true,
            'submenu' => [
                [
                    'title' => 'Account Receiveable',
                    'bullet' => 'dot',
                    'page' => '/',
                    'new-tab' => false,
                ],
                [
                    'title' => 'Laba / Rugi',
                    'bullet' => 'dot',
                    'page' => '/',
                    'new-tab' => false,
                ],
                [
                    'title' => 'General Ledger',
                    'bullet' => 'dot',
                    'page' => '/',
                    'new-tab' => false,
                ],
                [
                    'title' => 'Pengaturan Pelanggan',
                    'bullet' => 'dot',
                    'page' => '/',
                    'new-tab' => false,
                ],
            ]
        ],

        //Persediaan
        [
            'title' => 'Persediaan',
            'icon' => 'media/svg/icons/Shopping/Barcode-read.svg',
            'bullet' => 'dot',
            'root' => true,
            'submenu' => [
                [
                    'title' => 'Stok Barang',
                    'bullet' => 'dot',
                    'page' => '/',
                    'new-tab' => false,
                ],
                [
                    'title' => 'Adjustment Stok',
                    'bullet' => 'dot',
                    'page' => '/',
                    'new-tab' => false,
                ],
                [
                    'title' => 'Mutasi Stok',
                    'bullet' => 'dot',
                    'page' => '/',
                    'new-tab' => false,
                ],
            ]
        ],

        //Barang
        [
            'title' => 'Barang',
            'icon' => 'media/svg/icons/Shopping/Barcode-read.svg',
            'bullet' => 'dot',
            'root' => true,
            'submenu' => [
                [
                    'title' => 'Daftar Barang',
                    'bullet' => 'dot',
                    'page' => '/',
                    'new-tab' => false,
                ],
                [
                    'title' => 'Kategori Barang',
                    'bullet' => 'dot',
                    'page' => '/',
                    'new-tab' => false,
                ],
                [
                    'title' => 'Merk Barang',
                    'bullet' => 'dot',
                    'page' => '/',
                    'new-tab' => false,
                ],
                [
                    'title' => 'Satuan',
                    'bullet' => 'dot',
                    'page' => '/',
                    'new-tab' => false,
                ],
            ]
        ],

        //Pustaka
        [
            'title' => 'Pustaka',
            'icon' => 'media/svg/icons/Shopping/Barcode-read.svg',
            'bullet' => 'dot',
            'root' => true,
            'submenu' => [
                [
                    'title' => 'Daftar Pelanggan',
                    'bullet' => 'dot',
                    'page' => '/',
                    'new-tab' => false,
                ],
                [
                    'title' => 'Kategori Pelanggan',
                    'bullet' => 'dot',
                    'page' => '/',
                    'new-tab' => false,
                ],
                [
                    'title' => 'Daftar Supplier',
                    'bullet' => 'dot',
                    'page' => '/',
                    'new-tab' => false,
                ],
                [
                    'title' => 'Kategori Supplier',
                    'bullet' => 'dot',
                    'page' => '/',
                    'new-tab' => false,
                ],
                [
                    'title' => 'Daftar Sales',
                    'bullet' => 'dot',
                    'page' => '/',
                    'new-tab' => false,
                ],
                [
                    'title' => 'Ekspedisi Pengiriman',
                    'bullet' => 'dot',
                    'page' => '/',
                    'new-tab' => false,
                ],
                [
                    'title' => 'Daftar Bank',
                    'bullet' => 'dot',
                    'page' => '/',
                    'new-tab' => false,
                ],
                [
                    'title' => 'Rekening Perusahaan',
                    'bullet' => 'dot',
                    'page' => '/',
                    'new-tab' => false,
                ],
                [
                    'title' => 'Template TnC',
                    'bullet' => 'dot',
                    'page' => '/',
                    'new-tab' => false,
                ],
            ]
        ],

        //Pengaturan
        [
            'title' => 'Pengaturan',
            'icon' => 'media/svg/icons/Shopping/Barcode-read.svg',
            'bullet' => 'dot',
            'root' => true,
            'submenu' => [
                [
                    'title' => 'Preferensi',
                    'bullet' => 'dot',
                    'page' => '/',
                    'new-tab' => false,
                ],
                [
                    'title' => 'Daftar Pengguna',
                    'bullet' => 'dot',
                    'page' => '/',
                    'new-tab' => false,
                ],
                [
                    'title' => 'Daftar Menu',
                    'bullet' => 'dot',
                    'page' => '/',
                    'new-tab' => false,
                ],
                [
                    'title' => 'Pengaturan Hak Akses',
                    'bullet' => 'dot',
                    'page' => '/',
                    'new-tab' => false,
                ],
                [
                    'title' => 'Profile Saya',
                    'bullet' => 'dot',
                    'page' => '/',
                    'new-tab' => false,
                ],
            ]
        ],
    ]
];

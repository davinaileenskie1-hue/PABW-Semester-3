import { Head, useForm } from '@inertiajs/react';
import type { FormEvent } from 'react';
import AppLayout from '@/layouts/app-layout';
import type { BreadcrumbItem } from '@/types';
import { BukuForm } from '../components/buku-form';
import type { BukuFormData } from '../components/buku-form';

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Buku', href: '/buku' },
    { title: 'Tambah Buku', href: '/buku/create' },
];

export default function Create() {
    const { data, setData, post, processing, errors } = useForm<BukuFormData>({
        judul: '',
        penulis: '',
        penerbit: '',
        tahun_terbit: String(new Date().getFullYear()),
        stok: '0',
    });

    const submit = (event: FormEvent) => {
        event.preventDefault();
        post('/buku');
    };

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="Tambah Buku" />

            <div className="flex flex-1 flex-col gap-4 p-4">
                <div>
                    <h1 className="text-xl font-semibold">Tambah Buku</h1>
                    <p className="text-sm text-muted-foreground">Lengkapi data buku yang akan ditambahkan ke perpustakaan.</p>
                </div>

                <BukuForm
                    data={data}
                    errors={errors}
                    processing={processing}
                    submitLabel="Simpan"
                    setData={setData}
                    onSubmit={submit}
                />
            </div>
        </AppLayout>
    );
}

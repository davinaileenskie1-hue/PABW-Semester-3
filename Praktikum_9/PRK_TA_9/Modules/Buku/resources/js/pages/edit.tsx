import { Head, useForm } from '@inertiajs/react';
import type { FormEvent } from 'react';
import AppLayout from '@/layouts/app-layout';
import type { BreadcrumbItem } from '@/types';
import { BukuForm } from '../components/buku-form';
import type { BukuFormData } from '../components/buku-form';

type EditProps = {
    buku: {
        id: number;
        judul: string;
        penulis: string;
        penerbit: string;
        tahun_terbit: number;
        stok: number;
    };
};

export default function Edit({ buku }: EditProps) {
    const breadcrumbs: BreadcrumbItem[] = [
        { title: 'Buku', href: '/buku' },
        { title: 'Edit Buku', href: `/buku/${buku.id}/edit` },
    ];

    const { data, setData, put, processing, errors } = useForm<BukuFormData>({
        judul: buku.judul,
        penulis: buku.penulis,
        penerbit: buku.penerbit,
        tahun_terbit: String(buku.tahun_terbit),
        stok: String(buku.stok),
    });

    const submit = (event: FormEvent) => {
        event.preventDefault();
        put(`/buku/${buku.id}`);
    };

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="Edit Buku" />

            <div className="flex flex-1 flex-col gap-4 p-4">
                <div>
                    <h1 className="text-xl font-semibold">Edit Buku</h1>
                    <p className="text-sm text-muted-foreground">Perbarui data buku "{buku.judul}".</p>
                </div>

                <BukuForm
                    data={data}
                    errors={errors}
                    processing={processing}
                    submitLabel="Simpan Perubahan"
                    setData={setData}
                    onSubmit={submit}
                />
            </div>
        </AppLayout>
    );
}

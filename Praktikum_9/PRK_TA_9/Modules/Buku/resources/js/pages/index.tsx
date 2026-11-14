import { Head, Link, router } from '@inertiajs/react';
import { BookOpen, Pencil, Plus, Search, Trash2 } from 'lucide-react';
import { useState } from 'react';
import type { FormEvent } from 'react';
import { FlashMessage } from '@/components/alert-message';
import { ConfirmDialog } from '@/components/confirm-dialog';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { useSharedProps } from '@/hooks/use-shared-props';
import AppLayout from '@/layouts/app-layout';
import type { BreadcrumbItem } from '@/types';

type BukuRow = {
    id: number;
    judul: string;
    penulis: string;
    penerbit: string;
    tahun_terbit: number;
    stok: number;
};

type PaginationLink = { url: string | null; label: string; active: boolean };

type IndexProps = {
    books: {
        data: BukuRow[];
        links: PaginationLink[];
        from: number | null;
        to: number | null;
        total: number;
    };
    filters: { search: string };
};

const breadcrumbs: BreadcrumbItem[] = [{ title: 'Buku', href: '/buku' }];

export default function Index({ books, filters }: IndexProps) {
    const { isAdmin } = useSharedProps();
    const [search, setSearch] = useState(filters.search);
    const [deleting, setDeleting] = useState<BukuRow | null>(null);
    const [processing, setProcessing] = useState(false);

    // Kirim ulang request GET /buku dengan kata kunci pencarian
    const handleSearch = (event: FormEvent) => {
        event.preventDefault();

        router.get('/buku', { search }, { preserveState: true, replace: true });
    };

    const handleDelete = () => {
        if (!deleting) {
            return;
        }

        router.delete(`/buku/${deleting.id}`, {
            preserveScroll: true,
            onStart: () => setProcessing(true),
            onFinish: () => {
                setProcessing(false);
                setDeleting(null);
            },
        });
    };

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="Buku" />

            <div className="flex flex-1 flex-col gap-4 p-4">
                <div className="flex flex-wrap items-center justify-between gap-2">
                    <div>
                        <h1 className="flex items-center gap-2 text-xl font-semibold">
                            <BookOpen className="size-5" /> Data Buku <Badge variant="secondary">{books.total}</Badge>
                        </h1>
                        <p className="text-sm text-muted-foreground">
                            {isAdmin
                                ? 'Kelola koleksi buku perpustakaan kampus.'
                                : 'Daftar koleksi buku yang tersedia di perpustakaan.'}
                        </p>
                    </div>
                    {isAdmin && (
                        <Button asChild>
                            <Link href="/buku/create">
                                <Plus /> Tambah Buku
                            </Link>
                        </Button>
                    )}
                </div>

                <FlashMessage />

                <form onSubmit={handleSearch} className="flex max-w-md gap-2">
                    <Input
                        value={search}
                        onChange={(event) => setSearch(event.target.value)}
                        placeholder="Cari judul, penulis, atau penerbit..."
                    />
                    <Button type="submit" variant="secondary">
                        <Search /> Cari
                    </Button>
                </form>

                <div className="overflow-x-auto rounded-xl border">
                    <table className="w-full text-sm">
                        <thead className="bg-muted/50 text-left">
                            <tr>
                                <th className="px-4 py-3 font-medium">No</th>
                                <th className="px-4 py-3 font-medium">Judul</th>
                                <th className="px-4 py-3 font-medium">Penulis</th>
                                <th className="px-4 py-3 font-medium">Penerbit</th>
                                <th className="px-4 py-3 font-medium">Tahun Terbit</th>
                                <th className="px-4 py-3 font-medium">Stok</th>
                                {isAdmin && <th className="px-4 py-3 text-right font-medium">Aksi</th>}
                            </tr>
                        </thead>
                        <tbody>
                            {books.data.length === 0 && (
                                <tr>
                                    <td colSpan={isAdmin ? 7 : 6} className="px-4 py-8 text-center text-muted-foreground">
                                        {filters.search === '' ? 'Belum ada data buku.' : 'Data buku tidak ditemukan.'}
                                    </td>
                                </tr>
                            )}
                            {books.data.map((book, index) => (
                                <tr key={book.id} className="border-t">
                                    <td className="px-4 py-3">{(books.from ?? 1) + index}</td>
                                    <td className="px-4 py-3 font-medium">{book.judul}</td>
                                    <td className="px-4 py-3">{book.penulis}</td>
                                    <td className="px-4 py-3">{book.penerbit}</td>
                                    <td className="px-4 py-3">{book.tahun_terbit}</td>
                                    <td className="px-4 py-3">
                                        <Badge variant={book.stok > 0 ? 'secondary' : 'destructive'}>{book.stok}</Badge>
                                    </td>
                                    {isAdmin && (
                                        <td className="px-4 py-3">
                                            <div className="flex justify-end gap-2">
                                                <Button asChild size="sm" variant="outline">
                                                    <Link href={`/buku/${book.id}/edit`}>
                                                        <Pencil /> Edit
                                                    </Link>
                                                </Button>
                                                <Button size="sm" variant="destructive" onClick={() => setDeleting(book)}>
                                                    <Trash2 /> Hapus
                                                </Button>
                                            </div>
                                        </td>
                                    )}
                                </tr>
                            ))}
                        </tbody>
                    </table>
                </div>

                <div className="flex flex-wrap items-center justify-between gap-2 text-sm">
                    <p className="text-muted-foreground">
                        Menampilkan {books.from ?? 0}-{books.to ?? 0} dari {books.total} buku
                    </p>
                    <div className="flex flex-wrap gap-1">
                        {books.links.map((link, index) =>
                            link.url ? (
                                <Button key={index} asChild size="sm" variant={link.active ? 'default' : 'outline'}>
                                    <Link href={link.url} preserveScroll>
                                        {cleanLabel(link.label)}
                                    </Link>
                                </Button>
                            ) : (
                                <Button key={index} size="sm" variant="outline" disabled>
                                    {cleanLabel(link.label)}
                                </Button>
                            ),
                        )}
                    </div>
                </div>
            </div>

            <ConfirmDialog
                open={deleting !== null}
                description={`Yakin ingin menghapus buku "${deleting?.judul}"? Tindakan ini tidak dapat dibatalkan.`}
                processing={processing}
                onConfirm={handleDelete}
                onOpenChange={(open) => !open && setDeleting(null)}
            />
        </AppLayout>
    );
}

// Label pagination Laravel masih berisi entitas HTML &laquo; dan &raquo;
function cleanLabel(label: string): string {
    return label.replace('&laquo;', '«').replace('&raquo;', '»');
}

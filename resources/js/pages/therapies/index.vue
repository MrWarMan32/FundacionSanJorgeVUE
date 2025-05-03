<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import {Head, usePage, Link, router} from '@inertiajs/vue3';
import {Therapy, type BreadcrumbItem, type SharedData} from '@/types';
import {Table, TableBody, TableCell, TableCaption, TableEmpty, TableFooter, TableHeader, TableRow, TableHead} from '@/components/ui/table';
import {Button} from '@/components/ui/button';

import {Pencil, Trash, UserRoundPlus, Eye, UserCheck} from 'lucide-vue-next';
import {computed} from 'vue';
import Swal from 'sweetalert2';



interface TherapyPageProps extends SharedData{
    therapy : Therapy[];
}

const {props} = usePage<TherapyPageProps>();

const breadcrumbs: BreadcrumbItem[] = [
    {title: 'Terapias', href: '#'}
];


const deleteTherapy = async (id: number) => {
    Swal.fire({
        title: '¿Eliminar Terapia?',
        text: 'Esta acción no se puede deshacer!',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'No, cancelar',
    }).then(async (result) => {
        if (result.isConfirmed) {
            try {
                await router.delete(route('therapies.destroy', id), {
                    preserveScroll: true,
                    preserveState: true,
                    onSuccess: () => {
                        router.visit(route('therapies.index'));
                        Swal.fire(
                            '¡Eliminado!',
                            'La terapia ha sido eliminado con éxito.',
                            'success'
                        );
                        console.log('Terapia eliminado con éxito.');
                    },
                    onError: (errors) => {
                        let errorMessage = 'Hubo un error al intentar eliminar la terapia.';
                        if (errors && typeof errors === 'object') {
                            for (const key in errors) {
                                const errorValue = errors[key];
                                errorMessage += `<br><strong>${key}:</strong> ${Array.isArray(errorValue) ? errorValue.join(', ') : errorValue}`;
                            }
                        }
                        Swal.fire({
                            title: 'Error',
                            html: errorMessage,
                            icon: 'error',
                            confirmButtonText: 'Ok',
                        });
                    },
                });
            } catch (error: any) {
                Swal.fire(
                    '¡Error!',
                    'Ocurrió un error inesperado.',
                    'error'
                );
            }
        }
    });
};

</script>

<template>
    <Head title="Aspirantes" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="p-4">
            <div class="flex">
                <Button as-child size="sm" class="bg-indigo-500 text-white hover:bg-indigo-700">
                    <Link href="/therapies/create">
                        <UserRoundPlus /> Agregar Terapia
                    </Link>
                </Button>
            </div>
        </div>

        <div class="ralative min-h-[100vh] flex-1 rounded-xl border border-sidebar-border/70 dark:border-sidebar-border md:min-h-min">
            <Table>
               <TableCaption>Lista de Terapias</TableCaption>
                <TableHeader>
                    <TableRow>
                        <TableHead class="w-[200px] text-center">Nombre</TableHead>
                        <TableHead class="w-[200px] text-center">Descripcion</TableHead>
                        <TableHead class="w-[200px] text-center">Duracion</TableHead>
                        <TableHead class="w-[200px] text-center">Acciones</TableHead>
                    </TableRow>
                </TableHeader>

                <TableBody>
                    <template v-if="props.therapy.length > 0">
                        <TableRow v-for="therapy in props.therapy" :key="therapy.id" class="text-center">
                            <TableCell>{{ therapy.name }}</TableCell>
                            <TableCell>{{ therapy.description }}</TableCell>
                            <TableCell>{{ therapy.duration }}</TableCell>
                            <TableCell class="flex justify-center items-center h-full gap-2">

                                <Button as-child size="sm" class="bg-indigo-500 text-white hover:bg-indigo-700">
                                    <Link :href="route('therapies.edit', { therapy: therapy.id })">
                                        <Pencil />
                                    </Link>
                                </Button>
                                
                                <!-- <Button as-child size="sm" class="bg-yellow-500 text-white hover:bg-yellow-700">
                                    <Link :href="`/users/${user.id}/show`"><Eye /></Link>
                                </Button> -->

                                <Button size="sm" class="bg-red-500 text-white hover:bg-red-700" @click="deleteTherapy(therapy.id)">
                                    <Trash /> 
                                </Button>

                            </TableCell>
                        </TableRow>
                    </template>
                    <template v-else>
                        <TableEmpty> No hay terapias registrados. </TableEmpty>
                    </template>
                </TableBody>
            </Table>
        </div>
    </AppLayout>
</template>

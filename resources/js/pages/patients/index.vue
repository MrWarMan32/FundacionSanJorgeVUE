<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import {Head, usePage, Link, router} from '@inertiajs/vue3';
import {User, type BreadcrumbItem, type SharedData} from '@/types';
import {Table, TableBody, TableCell, TableCaption, TableEmpty, TableFooter, TableHeader, TableRow, TableHead} from '@/components/ui/table';
import {Button} from '@/components/ui/button';

import {Pencil, Eye, UserX} from 'lucide-vue-next';
import {computed} from 'vue';


interface UsersPageProps extends SharedData{
    users : User[];
}

const {props} = usePage<UsersPageProps>();

// Filtrar solo pacientes
const filteredUsers = computed(() => {
    return props.users.filter(user => {
        return user.user_type === 'usuario' && user.status === 'paciente';
    });
});

const convertToAspirante = async (id: number) => {
    if (confirm('¿Estás seguro de que deseas convertir a este paciente en aspirante?')) {
        try {
            await router.patch(
                route('patients.convertToAspirante', id),
                {}, // Datos (vacío, ya que solo estamos actualizando el estado)
                { // Opciones
                    preserveScroll: true,
                    preserveState: true,
                    onSuccess: () => {
                        router.visit(route('patients.index')); // Recargar la lista para ver el cambio
                        console.log('Paciente convertido a aspirante con éxito.');
                    },
                    onError: (errors) => {
                        console.error('Error al convertir paciente:', errors);
                        alert('Hubo un error al intentar convertir a aspirante.');
                    },
                }
            );
        } catch (error: any) {
            console.error('Error inesperado:', error);
            alert('Ocurrió un error inesperado.');
        }
    }
};



const breadcrumbs: BreadcrumbItem[] = [
    
    {title: 'Pacientes', href: '/patients'}
];

const deleteUser = async (id: number) => {
    if (confirm('¿Estás seguro de que deseas eliminar este aspirante?')) {
        router.delete(route('users.destroy', id), {
            preserveScroll: true,
            preserveState: true,
            onSuccess: () => {
                router.visit(route('users.index'));
                console.log('Aspirante eliminado con éxito.');
            },
            onError: () => {
                console.error('Error al eliminar el aspirante.');
            }
        });
    }
};  

</script>

<template>
    <Head title="Aspirantes" />
    <AppLayout :breadcrumbs="breadcrumbs">
       
        <div class="ralative min-h-[100vh] flex-1 rounded-xl border border-sidebar-border/70 dark:border-sidebar-border md:min-h-min">
            <Table>
               <TableCaption>Lista de Pacientes</TableCaption>
                <TableHeader>
                    <TableRow>
                        <TableHead class="w-[200px] text-center">Nombres</TableHead>
                        <TableHead class="w-[200px] text-center">Cedula</TableHead>
                        <TableHead class="w-[200px] text-center">Tipo Discapacidad</TableHead>
                        <TableHead class="w-[200px] text-center">Diagnostico</TableHead>
                        <TableHead class="w-[200px] text-center">Contacto</TableHead>
                        <TableHead class="w-[200px] text-center">Acciones</TableHead>
                    </TableRow>
                </TableHeader>

                <TableBody>
                    <template v-if="filteredUsers.length > 0">
                        <TableRow v-for="user in filteredUsers" :key="user.id" class="text-center">
                            <TableCell>{{ user.name }}</TableCell>
                            <TableCell>{{ user.id_card }}</TableCell>
                            <TableCell>
                                <div class="flex flex-wrap justify-center gap-1">
                                <template v-if="user.disability_type">
                                    <template v-for="(disability, index) in JSON.parse(user.disability_type)">
                                    <div v-if="index % 2 === 0" class="flex justify-center w-full">
                                        <span :key="index" class="bg-blue-100 text-indigo-600 text-xs font-semibold px-2.5 py-0.5 rounded-full dark:bg-indigo-600 dark:text-gray-300 mr-1">
                                        {{ disability }}
                                        </span>
                                        <span v-if="JSON.parse(user.disability_type)[index + 1]" :key="index + 1" class="bg-blue-100 text-indigo-600 text-xs font-semibold px-2.5 py-0.5 rounded-full dark:bg-indigo-600 dark:text-gray-300 mr-1">
                                        {{ JSON.parse(user.disability_type)[index + 1] }}
                                        </span>
                                    </div>
                                    </template>
                                </template>
                                <template v-else>
                                    <span class="bg-blue-100 text-indigo-600 text-xs font-semibold px-2.5 py-0.5 rounded-full dark:bg-indigo-600 dark:text-gray-300 mr-1">No especificado</span>
                                </template>
                                </div>
                            </TableCell>
                            <TableCell>{{ user.diagnosis }}</TableCell>
                            <TableCell>{{ user.phone }}</TableCell>
                            <TableCell class="flex justify-center gap-2">

                                <Button size="sm" class="bg-red-500 text-white hover:bg-red-700" @click="convertToAspirante(user.id)">
                                    <UserX />
                                </Button>

                                <Button as-child size="sm" class="bg-indigo-500 text-white hover:bg-indigo-700">
                                    <Link :href="`/users/${user.id}/edit`"><Pencil /></Link>
                                </Button>
                                
                                <Button as-child size="sm" class="bg-yellow-500 text-white hover:bg-yellow-700">
                                    <Link :href="`/users/${user.id}/show`"><Eye /></Link>
                                </Button>

                            </TableCell>
                        </TableRow>
                    </template>
                    <template v-else>
                        <TableEmpty> No hay pacientes registrados. </TableEmpty>
                    </template>
                </TableBody>
            </Table>
        </div>
    </AppLayout>
</template>

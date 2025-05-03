<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import {Head, usePage} from '@inertiajs/vue3';
import {Address, type BreadcrumbItem, type SharedData} from '@/types';
import {Table, TableBody, TableCell, TableCaption, TableEmpty, TableFooter, TableHeader, TableRow, TableHead} from '@/components/ui/table';

interface AddressesPageProps extends SharedData{
    address : Address[];
}

const {props} = usePage<AddressesPageProps>();

const breadcrumbs: BreadcrumbItem[] = [
    {title: 'Direcciones', href: '#'}
];


</script>

<template>
    <Head title="Direcciones" />
    <AppLayout :breadcrumbs="breadcrumbs">

        <div class="ralative min-h-[100vh] flex-1 rounded-xl border border-sidebar-border/70 dark:border-sidebar-border md:min-h-min">
            <Table>
               <TableCaption>Lista de Direcciones</TableCaption>
                <TableHeader>
                    <TableRow>
                        <TableHead class="w-[200px] text-center">Paciente</TableHead>
                        <TableHead class="w-[200px] text-center">Provincia</TableHead>
                        <TableHead class="w-[200px] text-center">Canton</TableHead>
                        <TableHead class="w-[200px] text-center">Parroquia</TableHead>
                        <TableHead class="w-[200px] text-center">Lugar</TableHead>
                        <TableHead class="w-[200px] text-center">Referencia</TableHead>
                    </TableRow>
                </TableHeader>

                <TableBody>
                    <template v-if="props.address.length > 0">
                        <TableRow v-for="address in props.address" :key="address.id" class="text-center">
                            <TableCell>{{ address.user?.name }} {{ address.user?.last_name }}</TableCell>
                            <TableCell>{{ address.province?.name_province }}</TableCell>
                            <TableCell>{{ address.canton?.name_canton }}</TableCell>
                            <TableCell>{{ address.parish?.parroquia }}</TableCell>
                            <TableCell>{{ address.site }}</TableCell>
                            <TableCell>{{ address.reference }}</TableCell>
                        </TableRow> 
                    </template>
                    <template v-else>
                        <TableEmpty> No hay direcciones registradas. </TableEmpty>
                    </template>
                </TableBody>
            </Table>
        </div>
    </AppLayout>
</template>

<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';
import PlaceholderPattern from '../components/PlaceholderPattern.vue';
import BarChart from '@/components/BarChart.vue';
import LineChart from '@/components/LineChart.vue';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: '/dashboard',
    },
];

const props = defineProps<{
    stats: {
        total_patients: number;
        male_patients: number;
        female_patients: number;

        total_users: number;
        male_users: number;
        female_users: number;

        total_doctors: number;

        total_patients_fisica : number;
    };
}>();

const chartData = {
  labels: ['Usuarios', 'Hombres', 'Mujeres', 'Terapia Fisica'], // Tus etiquetas del eje X
  datasets: [
    {
      label: 'Pacientes',
      data: [
        props.stats.total_patients,    // Valor para 'Usuarios'
        props.stats.male_patients,     // Valor para 'Hombres'
        props.stats.female_patients,   // Valor para 'Mujeres'
        0                              // Rellenar con 0 para 'Terapia Fisica', ya que no aplica a esta categoría
      ],
      backgroundColor: ['#4F46E5']
    },
    {
      label: 'Aspirantes',
      data: [
        props.stats.total_users,       // Valor para 'Usuarios'
        props.stats.male_users,        // Valor para 'Hombres'
        props.stats.female_users,      // Valor para 'Mujeres'
        0                              // Rellenar con 0 para 'Terapia Fisica'
      ],
      backgroundColor: ['#60A5FA']
    },
    {
      label: 'Doctores',
      data: [
        props.stats.total_doctors,     // Valor para 'Usuarios'
        0,                             // Rellenar con 0 para 'Hombres'
        0,                             // Rellenar con 0 para 'Mujeres'
        0                              // Rellenar con 0 para 'Terapia Fisica'
      ],
      backgroundColor: ['#F8C3A4']
    },
    {
      label: 'Terapia Fisica', // Este dataset solo tiene un valor para la etiqueta "Terapia Fisica"
      data: [
        0,                               // Rellenar con 0 para 'Usuarios'
        0,                               // Rellenar con 0 para 'Hombres'
        0,                               // Rellenar con 0 para 'Mujeres'
        props.stats.total_patients_fisica // El valor real para la etiqueta 'Terapia Fisica'
      ],
      backgroundColor: ['#F8C3A5']
    }
  ],
};

const chartOptions = {
  responsive: true,
  plugins: {
    legend: {
      position: 'top'as const,
    },
    title: {
      display: true,
      text: 'Estadísticas',
    },
  },
};

</script>

<template>
    <Head title="Dashboard" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
            <div class="grid auto-rows-min gap-4 md:grid-cols-3">
                <div class="relative aspect-video overflow-hidden rounded-xl border border-sidebar-border/70 dark:border-sidebar-border">
                    <PlaceholderPattern />
                </div>
                <div class="relative aspect-video overflow-hidden rounded-xl border border-sidebar-border/70 dark:border-sidebar-border">
                    <PlaceholderPattern />
                </div>
                <div class="relative aspect-video overflow-hidden rounded-xl border border-sidebar-border/70 dark:border-sidebar-border">
                    <PlaceholderPattern />
                </div>
            </div>
            <div class="relative min-h-[100vh] flex-1 rounded-xl border border-sidebar-border/70 dark:border-sidebar-border md:min-h-min p-6">
                <div class="w-full h-full bg-white rounded-xl shadow dark:bg-gray-900/0">
                    <BarChart :chart-data="chartData" :chart-options="chartOptions"/>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

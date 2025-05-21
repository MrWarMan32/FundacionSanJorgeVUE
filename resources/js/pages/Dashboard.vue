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
    };
}>();

const chartData = {
     backgroundColor: 'transparent',
  labels: ['Usuarios', 'Hombres', 'Mujeres'],
  datasets: [
    {
      label: 'Pacientes',
      data: [
        props.stats.total_patients,
        props.stats.male_patients,
        props.stats.female_patients
      ],
      backgroundColor: ['#4F46E5']
    },
    {
      label: 'Aspirantes',
      data: [
        props.stats.total_users,
        props.stats.male_users,
        props.stats.female_users
      ],
      backgroundColor: ['#60A5FA']
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
      text: 'Estadísticas de Pacientes y Aspirantes',
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
                <div class="w-full h-full bg-white rounded-xl shadow dark:bg-gray-900">
                    <BarChart :chart-data="chartData" :chart-options="chartOptions"/>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

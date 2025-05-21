<script setup lang="ts">
import {
  Chart as ChartJS,
  Title,
  Tooltip,
  Legend,
  LineElement,
  CategoryScale,
  LinearScale,
  PointElement,
} from 'chart.js';
import { Line } from 'vue-chartjs';

ChartJS.register(Title, Tooltip, Legend, LineElement, CategoryScale, LinearScale, PointElement);

const props = defineProps<{
  chartData: {
    labels: string[],
    datasets: { label: string, data: number[], borderColor: string, backgroundColor?: string }[],
  },
}>();
</script>

<template>
  <Line :data="chartData" />
</template>

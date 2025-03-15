import {defineStore} from 'pinia'
import {ref} from "vue";
import {doctorDelaySurgery, doctorIndex, doctorUpdate, surgeryIndex} from "../api";
import dayjs from 'dayjs';

const statusMap = {
    0: '可面诊',
    1: '手术中',
    2: '休息'
}
const statusColor = {
    0: 'green',
    1: 'red',
    2: 'gray'
}
const parseDoctorItem = (item) => {
    item.statusText = statusMap[item.status];
    // 计算当前时间 在 start_date ,end_date 之间进度是多少, 如果超过 100% 已 100 为上限,
    let start = dayjs(item.start_date);
    let end = dayjs(item.end_date);
    let now = dayjs();

    item.progress = Math.min(100, Math.max(0, ((now.diff(start, 'minute')) / (end.diff(start, 'minute'))) * 100));
    item.surgery_name = item.surgery?.name || '暂无手术';
    item.statusColor = statusColor[item.status];
    item.start_time = start.format('HH:mm');
    item.end_time = end.format('HH:mm');
    // 🚀
    return item;
}

export const useDoctorStore = defineStore('doctors', () => {
    const doctorList = ref([])
    const surgeries = ref([])
    const loading = ref(false);
    const surgeryLoading = ref(false);

    const fetchSurgeries = async () => {
        if (surgeryLoading.value) return;
        surgeryLoading.value = true;
        let response = await surgeryIndex();
        surgeries.value = response.data?.data || [];
        surgeryLoading.value = false;
    }
    const fetchDoctors = async () => {
        if (loading.value) return;
        loading.value = true;
        let response = await doctorIndex();
        console.log("response", response);
        const data = response.data?.data || [];
        // 以 status 排序 , 等于 1 排第一. 等于 0 排第二 ,等于 2 排第三
        doctorList.value = data.map(parseDoctorItem);
        sortDoctorList();
        loading.value = false;
    }

    const order = {1: 0, 0: 1, 2: 2}; // 定义排序权重
    const sortDoctorList = () => {
        doctorList.value.sort((a, b) => {
            return order[a.status] - order[b.status];
        });
    }

    const updateDoctor = (id, params) => {
        doctorList.value = doctorList.value.map(doctor =>
            doctor.id === id ? {...doctor, ...params} : doctor
        );
    }

    const endSurgery = async (id) => {
        await doctorNormal(id);
    }

    const startSurgery = async (id, params) => {
        params.status = 1;
        let response = await doctorUpdate(id, params);
        console.log("response",response);
        return response;
    }

    const doctorRest = async (id) => {
        await doctorSwitchStatus(id, 2);
    }

    const delaySurgery = async (id ,params) => {
        let response = await doctorDelaySurgery(id , params)
        let end = dayjs(params.end_date);
        updateDoctor(id, {
            ...params,
            end_time:end.format('HH:mm'),
        })
        return response;
    }

    const doctorSwitchStatus = async (id , status) => {
        let response = await doctorUpdate(id, {status: status});
        updateDoctor(id, {
            status: status,
            statusText: statusMap[status],
            statusColor: statusColor[status]
        })
        sortDoctorList();
    }

    const doctorNormal = async (id) => {
        await doctorSwitchStatus(id, 0);
    }

    return {
        doctorList,
        surgeries,
        surgeryLoading,
        loading,
        fetchDoctors,
        fetchSurgeries,
        endSurgery,
        startSurgery,
        doctorRest,
        delaySurgery,
        doctorNormal
    }
})

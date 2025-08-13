<template>
    <div>
        <h2 class="page-title">
            🚀 {{ doctorStore.department?.name || '' }}医生手术动态表
        </h2>
        <h4 style="margin: 0;padding-left: 25px;">
            {{
                dayjs().format('YYYY-MM-DD')
            }}
        </h4>
        <List item-layout="horizontal"
              :loading="doctorStore.loading"
              :data-source="doctorStore.doctorList">
            <template #renderItem="{ item }">
                <ListItem>
                    <Space direction="vertical" style="width:100%">
                        <BadgeRibbon :text="item.statusText" :color="item.statusColor">
                            <Card hoverable class="w-full">
                                <template #title>
                                    <div>
                                        <b style="margin-right:10px;">{{ item.name }}</b>
                                        <Tag v-if="item.status === 1 && item.surgery_room" color="#f50">
                                            手术间{{ item.surgery_room }}
                                        </Tag>
                                    </div>
                                </template>
                                <div v-if="item.status === 1 && item.surgery_name ">
                                    <Space direction="vertical" style="padding-bottom:10px;">
                                        <div>
                                            当前手术项目:
                                            <Tag color="#f50">{{ item.surgery_name }}</Tag>
                                        </div>
                                        <div>
                                            手术开始时间:
                                            <Tag color="orange">{{ item.start_time }}</Tag>
                                        </div>
                                        <div>
                                            手术预计结束时间:
                                            <Tag color="orange">{{ item.end_time }}</Tag>
                                        </div>
                                    </Space>
                                    <Progress :percent="item.progress" :show-info="false"/>
                                    <p v-if="item.progress === 100"
                                       style="color: rgb(153 143 143);padding-top: 10px;margin:0;">
                                        手术可能已经结束.
                                    </p>
                                </div>
                            </Card>
                        </BadgeRibbon>
                    </Space>

                </ListItem>
            </template>
            <template #footer>
                <div v-if="!doctorStore.loading" style="text-align: center;">
                    <Button @click="reloadDoctor">刷新医生状态</Button>
                </div>
            </template>
        </List>
    </div>
</template>
<script setup>
import dayjs from "dayjs";
import {List, ListItem, Card, BadgeRibbon, Space, Progress, Tag, Button} from 'ant-design-vue';
import {useDoctorStore} from "../stores/doctor";
import {onMounted} from "vue";
import {useHead} from "@unhead/vue";
import {useRoute} from "vue-router";

const doctorStore = useDoctorStore();
const router = useRoute();

const reloadDoctor = () => {
    doctorStore.fetchDoctors();
}

onMounted(() => {
    if (router.query?.d_id)
        doctorStore.setDepartmentId(router.query.d_id);
    // 30 秒 刷新一次
    setInterval(() => {
        doctorStore.fetchDoctors();
    }, 30000);
    doctorStore.fetchDoctors();
})

useHead({
    title: '医生列表',
})

</script>
<style scoped lang="less">

</style>

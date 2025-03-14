<template>
    <div>
        <h2 class="page-title">
            🚀 整形外科医生手术动态表
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
                            <Card hoverable class="w-full" :title="item.name">
                                <div v-if="item.status === 1">
                                    <div style="padding-bottom:10px;">
                                        <b>
                                            当前手术项目:
                                            <Tag color="#f50">{{ item.surgery_name }}</Tag>
                                        </b>
                                        <b>
                                            手术预计时间:
                                            <Tag color="orange">{{ item.start_time }}</Tag>
                                            ~
                                            <Tag color="orange">{{ item.end_time }}</Tag>
                                        </b>
                                    </div>
                                    <Progress :percent="item.progress" :show-info="false"/>
                                    <p v-if="item.progress === 100" style="color: rgb(153 143 143);padding-top: 10px;margin:0;">
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

const doctorStore = useDoctorStore();

const reloadDoctor = () => {
    doctorStore.fetchDoctors();
}

onMounted(() => {
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

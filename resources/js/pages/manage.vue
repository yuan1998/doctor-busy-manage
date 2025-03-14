<template>
    <div>
        <h2 class="page-title">
            🚀 医生状态管理
        </h2>

        <List item-layout="horizontal"
              :loading="doctorStore.loading"
              :data-source="doctorStore.doctorList">
            <template #renderItem="{ item }">
                <ListItem>
                    <Space direction="vertical" style="width:100%">
                        <BadgeRibbon :text="item.statusText" :color="item.statusColor">
                            <Card hoverable class="w-full" >
                                <template #title>
                                    <div>
                                        <b>{{item.name}}</b> <Tag v-if="item.status === 1" color="#f50">手术间{{ item.surgery_room }}</Tag>
                                    </div>
                                </template>
                                <Space v-if="item.status === 0">
                                    <Button type="primary" @click="showDrawer(item)">开始手术</Button>
                                    <Button @click="handleRest(item.id)">休息</Button>
                                </Space>
                                <Space v-if="item.status === 2">
                                    <Button type="primary" @click="handleDoctorWork(item.id)">上班</Button>
                                </Space>
                                <div v-if="item.status === 1">
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
                                    <p v-if="item.progress === 100" style="color: rgb(153 143 143);padding:10px 0;margin:0;">
                                        手术可能已经结束.
                                    </p>
                                    <Space>
                                        <Button danger type="primary" @click="handleEndSurgery(item.id)">结束手术</Button>
<!--                                        <Button >推迟结束时间</Button>-->

                                    </Space>

                                </div>
                            </Card>
                        </BadgeRibbon>
                    </Space>

                </ListItem>
            </template>
            <template #footer>
                <div v-if="!doctorStore.loading" style="text-align: center;">
                    <Button @click="reloadDoctor">刷新医生列表</Button>
                </div>
            </template>
        </List>

        <Drawer size="large" title="医生手术!启动!" placement="bottom" :loading="submitLoading" :open="open" @close="onClose">
            <template #extra>
                <Button style="margin-right: 8px" :disbabled="submitLoading" @click="onDrawerCancel">取消</Button>
                <Button type="primary" :loading="submitLoading" @click="handleOnSubmit">开始手术</Button>
            </template>
            <Spin :spinning="submitLoading">
                <Form
                    ref="formRef"
                    :model="formState"
                    :rules="rules"
                    :label-col="labelCol"
                    :wrapper-col="wrapperCol"
                    :loading="submitLoading"
                    v-if="activeItem"
                >
                    <FormItem label="医生名称">
                        <div> {{ activeItem.name }}</div>
                    </FormItem>
                    <FormItem ref="surgery_id" label="手术项目" name="surgery_id">
                        <Select
                            ref="select"
                            v-model:value="formState.surgery_id"
                            @change="handleChange"
                        >
                            <SelectOption v-for="(item) in doctorStore.surgeries" :key="item.id" :value="item.id">
                                {{ item.name }}
                            </SelectOption>
                        </Select>
                    </FormItem>
                    <FormItem ref="end_date" label="预计手术时间" name="end_date">
                        <TimePicker v-model:value="formState.end_date" format="HH:mm"/>
                    </FormItem>
                    <FormItem ref="surgery_room" label="手术室" name="surgery_room">
                        <RadioGroup v-model:value="formState.surgery_room" :options="plainOptions"/>
                    </FormItem>
                    <FormItem>
                        <Space>
                            <Button type="primary" :loading="submitLoading" @click="handleOnSubmit">开始手术</Button>
                            <Button :disbabled="submitLoading" @click="onDrawerCancel">取消</Button>
                        </Space>
                    </FormItem>
                </Form>
            </Spin>
        </Drawer>

        <Modal v-model:open="visbleModal" title="Title" >
            <p>ces</p>
        </Modal>
    </div>
</template>
<script setup>
import {
    List,
    ListItem,
    Card,
    BadgeRibbon,
    Space,
    Progress,
    Tag,
    Button,
    Modal,
    Drawer,
    Form,
    FormItem,
    Spin,
    RadioGroup,
    TimePicker,
    Select,
    SelectOption
} from 'ant-design-vue';
import {useDoctorStore} from "../stores/doctor";
import {onMounted, reactive, ref} from "vue";
import dayjs from "dayjs";
import {useHead} from "@unhead/vue";

const open = ref(false);
const visbleModal = ref(false);
const showConfirm = ref(false);
const submitLoading = ref(false);
const activeItem = ref(null);
const formRef = ref();
const formState = reactive({
    'surgery_id': null,
    'surgery_room': null,
    'end_date': null,
})

const labelCol = {span: 5};
const wrapperCol = {span: 13};
const rules = {
    surgery_id: [
        {required: true, message: '请选择手术项目', trigger: 'change'},
    ],
    end_date: [
        {required: true, message: '请选择手术时间', trigger: 'change'},
    ],
    surgery_room: [
        {required: true, message: '请选择手术室', trigger: 'change'},
    ],
}
const plainOptions = Array.from({length: 10}, (_, i) => ({label: `手术室${i + 1}`, value: i + 1}));
const doctorStore = useDoctorStore();


const handleChange = (val) => {
    let item = doctorStore.surgeries.find((item) => item.id === val);
    formState.end_date = dayjs().add(item.surgery_time, 'minute');
    // formState.range = [dayjs(), dayjs().add(item.surgery_time, 'minute')]
}

const showDrawer = (item) => {
    open.value = true;
    activeItem.value = item;
};
const onDrawerCancel = () => {
    if (submitLoading.value) return;
    open.value = false;
}

const handleOnSubmit = () => {
    if (submitLoading.value) return;
    formRef.value
        .validate()
        .then(async () => {
            submitLoading.value = true;
            let response = await doctorStore.startSurgery(activeItem.value.id, {
                surgery_id: formState.surgery_id,
                surgery_room: formState.surgery_room,
                start_date: dayjs().format('YYYY-MM-DD HH:mm:ss'),
                end_date: formState.end_date.format('YYYY-MM-DD HH:mm:ss'),
            });
            submitLoading.value = false;
            if (response.data.code === 0) {
                open.value = false;
                doctorStore.fetchDoctors();
            }
        })
        .catch(error => {
            console.log('error', error);
        });
}

const onClose = (e) => {
    if (submitLoading.value) return false;
    open.value = false;
    activeItem.value = null;
    resetForm();
};

const resetForm = () => {
    formRef.value.resetFields();
};

const handleRest = (id) => {
    Modal.confirm({
        title: '确认医生休息?',
        okText: "确认",
        cancelText: '取消',
        onOk() {
            // return new Promise((resolve, reject) => {
            //     setTimeout(Math.random() > 0.5 ? resolve : reject, 1000);
            // }).catch(() => console.log('Oops errors!'));
            return doctorStore.doctorRest(id);
        },
        onCancel() {
            console.log('Cancel');
        },
    });
}

const handleDoctorWork = (id) => {
    Modal.confirm({
        title: '确认医生上班?',
        okText: "确认",
        cancelText: '取消',
        onOk() {
            // return new Promise((resolve, reject) => {
            //     setTimeout(Math.random() > 0.5 ? resolve : reject, 1000);
            // }).catch(() => console.log('Oops errors!'));
            return doctorStore.doctorNormal(id);
        },
        onCancel() {
            console.log('Cancel');
        },
    });
}

const reloadDoctor = () => {
    doctorStore.fetchDoctors();
}

const handleEndSurgery = (id) => {
    Modal.confirm({
        title: '是否结束医生的手术?',
        okText: "确认",
        cancelText: '取消',
        onOk() {
            // return new Promise((resolve, reject) => {
            //     setTimeout(Math.random() > 0.5 ? resolve : reject, 1000);
            // }).catch(() => console.log('Oops errors!'));
            return doctorStore.endSurgery(id);
        },
        onCancel() {
            console.log('Cancel');
        },
    });
}

const handleStartSurgery = (id) => {

}


useHead({
    title: '医生状态管理',
})
onMounted(() => {
    // 30 秒 刷新一次
    // useInterval(() => {
    //     if (open.value || showConfirm.value) return;
    //     doctorStore.fetchDoctors();
    // }, 30000);

    doctorStore.fetchDoctors();
    doctorStore.fetchSurgeries();
})


const startSurgery = (id) => {
    doctorStore.startSurgery(id);
}
const endSurgery = (id) => {
    doctorStore.endSurgery(id);
}

</script>
<style scoped lang="less">

</style>

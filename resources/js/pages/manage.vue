<template>
    <div style="background: #ffffff;">
        <h2 class="page-title">
            🚀{{ doctorStore.department?.name || '' }}医生状态管理
        </h2>

        <List item-layout="horizontal"
              :loading="doctorStore.loading"
              :data-source="doctorStore.doctorList">
            <template #renderItem="{ item }">
                <ListItem>
                    <Space direction="vertical" style="width:100%">
                        <BadgeRibbon :text="item.statusText" :color="item.statusColor">
                            <Card hoverable class="w-full">
                                <template #title>
                                    <Space>
                                        <div>
                                            <b style="margin-right:10px;">{{ item.name }}</b>
                                            <Tag v-if="item.status === 1 && item.surgery_room" color="#f50">手术间{{ item.surgery_room }}</Tag>
                                        </div>
                                        <Button v-if="!singlePage" type="link" @click="toDoctorPage(item.id)">单独页</Button>
                                    </Space>
                                </template>
                                <Space v-if="item.status === 0">
                                    <Button type="primary" @click="handleStartSurge(item)">开始手术</Button>
                                    <Button type="primary" @click="handleStartWork(item)">开始面诊</Button>
                                    <Button @click="handleRest(item.id)">休息</Button>
                                </Space>
                                <Space v-if="item.status === 2">
                                    <Button type="primary" @click="handleDoctorWork(item.id)">上班</Button>
                                </Space>
                                <Space v-if="item.status === 3">
                                    <Button type="primary" @click="handleDoctorWork(item.id)">结束面诊</Button>
                                </Space>
                                <div v-if="item.status === 1">
                                    <template v-if="item.surgery_id">
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
                                           style="color: rgb(153 143 143);padding:10px 0;margin:0;">
                                            手术可能已经结束.
                                        </p>
                                    </template>
                                    <Space>
                                        <Button danger @click="handleEndSurgery(item.id)">结束手术
                                        </Button>
                                        <Button @click="handleDelaySurgery(item)">推迟结束时间</Button>
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

        <Modal v-model:open="open" :loading="submitLoading" :maskClosable="false">
            <template #footer>
                <Space>
                    <Button :disbabled="submitLoading" @click="onDrawerCancel">取消</Button>
                    <Button type="primary" :loading="submitLoading" @click="handleOnSubmit">开始手术</Button>
                </Space>
            </template>
            <template #title>
                {{ activeItem.name }} 医生!启动!
            </template>
            <Spin :spinning="submitLoading">
                <Form
                    style="padding-top: 30px;"
                    ref="formRef"
                    :model="formState"
                    :rules="rules"
                    :labelWrap="true"
                    :loading="submitLoading"
                    v-if="activeItem"
                >
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
                    <FormItem ref="end_date" label="预计手术结束时间" name="end_date">
                        <TimePicker v-model:value="formState.end_date" format="HH:mm"/>
                    </FormItem>
                    <FormItem ref="surgery_room" label="手术室" name="surgery_room">
                        <RadioGroup v-model:value="formState.surgery_room" :options="plainOptions"/>
                    </FormItem>
                </Form>
            </Spin>
        </Modal>

        <Modal v-model:open="visbleModal" title="推迟手术时间">
            <RadioGroup v-model:value="delayEndTime" :disabled="delaySurgeryLoading">
                <Radio :style="radioStyle" :value="10">10 分钟</Radio>
                <Radio :style="radioStyle" :value="20">20 分钟</Radio>
                <Radio :style="radioStyle" :value="30">30 分钟</Radio>
                <Radio :style="radioStyle" :value="0">
                    其他
                    <template v-if="delayEndTime === 0">
                        <InputNumber :min="1"
                                     v-model:value="delayEndTimeMore"
                                     addon-after="分钟"
                                     style="width: 150px; margin-left: 10px"/>
                    </template>
                </Radio>
            </RadioGroup>
            <template v-if="!!delayTimeErrorMsg">
                <p style="color: red;">{{ delayTimeErrorMsg }}</p>
            </template>
            <template #footer>
                <Space>
                    <Button :disbabled="delaySurgeryLoading" @click="onDelayModalCancel">取消</Button>
                    <Button type="primary" :loading="delaySurgeryLoading" @click="handleDelaySurgerySubmit">确认
                    </Button>
                </Space>
            </template>
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
    Radio,
    InputNumber,
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
import {useRoute, useRouter} from "vue-router";

const open = ref(false);
const visbleModal = ref(false);
const showConfirm = ref(false);
const delayTimeErrorMsg = ref("");
const delaySurgeryLoading = ref(false);
const submitLoading = ref(false);
const activeItem = ref(null);
const delayActiveItem = ref(null);
const formRef = ref();
const singlePage = ref(false);
const formState = reactive({
    'surgery_id': null,
    'surgery_room': null,
    'end_date': null,
})
const radioStyle = reactive({
    display: 'flex',
    height: '30px',
    lineHeight: '30px',
});
// 推迟结束时间
const delayEndTime = ref(1);
const delayEndTimeMore = ref(1);
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
const route = useRoute();
const router = useRouter();


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

const onDelayModalCancel = () => {
    visbleModal.value = false;
    delayActiveItem.value = null;
    delayTimeErrorMsg.value = '';
}

const handleRest = (id) => {
    doctorStore.doctorRest(id)
}
const handleStartWork = (item) => {
    doctorStore.doctorStartWork(item.id);
}
const handleStartSurge = (item) => {
    doctorStore.doctorStartSurge(item.id);
}


const handleDoctorWork = (id) => {
    doctorStore.doctorNormal(id);
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

const handleDelaySurgery = (item) => {
    delayActiveItem.value = item;
    visbleModal.value = true;
    delayEndTime.value = 30;
    delayEndTimeMore.value = 60;
}

const handleDelaySurgerySubmit = async () => {
    if (!delayEndTime.value && !delayEndTimeMore.value) {
        delayTimeErrorMsg.value = "请输入正确的时间";
        return;
    }
    delayTimeErrorMsg.value = '';
    let minutes = delayEndTime.value || delayEndTimeMore.value;
    let endDate = dayjs(delayActiveItem.value.end_date).add(minutes, 'minute').format('YYYY-MM-DD HH:mm:ss');
    delaySurgeryLoading.value = true;
    let response = await doctorStore.delaySurgery(delayActiveItem.value.id, {
        'end_date': endDate
    })
    delaySurgeryLoading.value = false;
    console.log("response", response);
    if (response.data.code === 0) {
        onDelayModalCancel();
    }

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
    if (route.query?.d_id)
        doctorStore.setDepartmentId(route.query.d_id);
    if (route.query?.doctor_id) {
        singlePage.value = true;
        doctorStore.setDoctorId(route.query.doctor_id);
    }

    doctorStore.fetchDoctors();
    doctorStore.fetchSurgeries();
})

const toDoctorPage = (id) => {
    // // 跳转到 /manage?doctor_id={id}，并刷新页面数据
    router.push({path: '/manage', query: {doctor_id: id}});
    doctorStore.setDoctorId(id);
    singlePage.value = true;
    reloadDoctor();

}


const startSurgery = (id) => {
    doctorStore.startSurgery(id);
}
const endSurgery = (id) => {
    doctorStore.endSurgery(id);
}

</script>
<style scoped lang="less">

</style>

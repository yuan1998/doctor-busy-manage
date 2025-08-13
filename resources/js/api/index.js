import axios from 'axios';
import {notification} from 'ant-design-vue';

// doctor 的 index 和 update 方法
const instance = axios.create({
    headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
    }
});
// 在接收到 response 的时候获取 msg ,如果 msg 不是 OK, 打印出来
instance.interceptors.response.use(
    response => {
        if (response.data?.msg !== 'OK') {
            notification.success({
                message: 'Great!',
                description: response.data.msg,
            });
            console.log(response.data.msg);
        }
        return response;
    },
    error => {
        return Promise.reject(error);
    }
);

const requestGet = async (url, params) => {
    return await instance.get(url, {
        params: params
    });
}
const requestPost = async (url, params) => {
    return await instance.post(url, params);
}
const requestPut = async (url, params) => {
    return await instance.put(url, params);
}

export const doctorIndex = async (query) => {
    return await requestGet('/api/doctors',query);
}
export const doctorUpdate =  (id, params) => {
    return  requestPut(`/api/doctors/${id}`, params);
}
export const doctorDelaySurgery =  (id, params) => {
    return  requestPut(`/api/doctors/${id}/delay_surgery_end_time`, params);
}

export const surgeryIndex = async () => {
    return await requestGet('/api/surgeries');
}

// 订单退款API前端调用示例（小程序/JavaScript）

const API_BASE = 'https://your-domain.com/api'; // 替换为实际API地址

// 获取用户token（需要先登录）
const getToken = () => {
  return wx.getStorageSync('token') || '';
};

// 通用请求方法
const request = (url, data = {}, method = 'POST') => {
  return new Promise((resolve, reject) => {
    const token = getToken();
    
    wx.request({
      url: API_BASE + url,
      method: method,
      data: data,
      header: {
        'Content-Type': 'application/json',
        'Authorization': token ? 'Bearer ' + token : ''
      },
      success: (res) => {
        if (res.data.error === 0) {
          resolve(res.data);
        } else {
          reject(res.data.message || '请求失败');
        }
      },
      fail: (err) => {
        reject('网络请求失败');
      }
    });
  });
};

// 退款API服务类
class RefundService {
  
  // 1. 获取退款选项
  static async getOptions() {
    return await request('/order/order-refund/options');
  }
  
  // 2. 检查订单是否可以退款
  static async checkOrderRefund(orderSn) {
    return await request('/order/order-refund/check', {
      order_sn: orderSn
    });
  }
  
  // 3. 申请退款
  static async applyRefund(params) {
    return await request('/order/order-refund/apply', params);
  }
  
  // 4. 获取退款列表
  static async getRefundList(params = {}) {
    return await request('/order/order-refund/list', {
      state: params.state || 0,
      page: params.page || 1,
      page_size: params.pageSize || 20
    });
  }
  
  // 5. 获取退款详情
  static async getRefundDetail(refundSn) {
    return await request('/order/order-refund/detail', {
      refund_sn: refundSn
    });
  }
  
  // 6. 取消退款申请
  static async cancelRefund(refundId) {
    return await request('/order/order-refund/cancel', {
      refund_id: refundId
    });
  }
  
  // 7. 填写退货物流信息
  static async fillExpressInfo(refundId, expressName, expressNumber) {
    return await request('/order/order-refund/fill-express', {
      refund_id: refundId,
      express_name: expressName,
      express_number: expressNumber
    });
  }
}

// 使用示例
async function exampleUsage() {
  try {
    // 示例1：获取退款选项
    console.log('=== 获取退款选项 ===');
    const options = await RefundService.getOptions();
    console.log('退款类型:', options.data.types);
    console.log('退款原因:', options.data.reasons);
    
    // 示例2：检查订单是否可以退款
    console.log('\n=== 检查订单是否可以退款 ===');
    const checkResult = await RefundService.checkOrderRefund('202502261500001');
    console.log('是否可以退款:', checkResult.data.can_refund);
    console.log('订单状态:', checkResult.data.order_status_text);
    
    if (checkResult.data.can_refund) {
      // 示例3：申请退款
      console.log('\n=== 申请退款 ===');
      const applyResult = await RefundService.applyRefund({
        order_sn: '202502261500001',
        type: 1, // 仅退款
        name: '张三',
        mobile: '13800138000',
        money: 199.00,
        reason: 2, // 商品质量问题
        content: '商品有瑕疵，要求退款',
        image: 'image1.jpg,image2.jpg',
        goods_status: 1 // 未收到货
      });
      console.log('申请结果:', applyResult.message);
      console.log('退款ID:', applyResult.data.refund_id);
      
      // 示例4：获取退款列表
      console.log('\n=== 获取退款列表 ===');
      const listResult = await RefundService.getRefundList({
        state: 0, // 全部状态
        page: 1,
        pageSize: 10
      });
      console.log('退款总数:', listResult.data.total);
      console.log('退款列表:', listResult.data.list);
      
      if (listResult.data.list.length > 0) {
        const refund = listResult.data.list[0];
        
        // 示例5：获取退款详情
        console.log('\n=== 获取退款详情 ===');
        const detailResult = await RefundService.getRefundDetail(refund.order_number);
        console.log('退款详情:', detailResult.data.refund);
        console.log('订单信息:', detailResult.data.order);
        
        // 示例6：如果是待审核状态，可以取消
        if (refund.status === 1) {
          console.log('\n=== 取消退款申请 ===');
          const cancelResult = await RefundService.cancelRefund(refund.id);
          console.log('取消结果:', cancelResult.message);
        }
        
        // 示例7：如果是退货退款且审核通过，填写物流信息
        if (refund.type === 2 && refund.status === 2) {
          console.log('\n=== 填写退货物流信息 ===');
          const expressResult = await RefundService.fillExpressInfo(
            refund.id,
            '顺丰速运',
            'SF1234567890'
          );
          console.log('填写结果:', expressResult.message);
        }
      }
    }
    
  } catch (error) {
    console.error('API调用失败:', error);
  }
}

// 页面中使用示例
Page({
  data: {
    refundList: [],
    refundOptions: {},
    loading: false
  },
  
  onLoad() {
    this.loadRefundOptions();
    this.loadRefundList();
  },
  
  // 加载退款选项
  async loadRefundOptions() {
    try {
      const result = await RefundService.getOptions();
      this.setData({
        refundOptions: result.data
      });
    } catch (error) {
      wx.showToast({
        title: '加载选项失败',
        icon: 'error'
      });
    }
  },
  
  // 加载退款列表
  async loadRefundList() {
    this.setData({ loading: true });
    try {
      const result = await RefundService.getRefundList();
      this.setData({
        refundList: result.data.list,
        loading: false
      });
    } catch (error) {
      this.setData({ loading: false });
      wx.showToast({
        title: '加载列表失败',
        icon: 'error'
      });
    }
  },
  
  // 申请退款
  async handleApplyRefund(e) {
    const { orderSn } = e.currentTarget.dataset;
    
    // 先检查是否可以退款
    try {
      const checkResult = await RefundService.checkOrderRefund(orderSn);
      
      if (!checkResult.data.can_refund) {
        wx.showModal({
          title: '提示',
          content: checkResult.data.message,
          showCancel: false
        });
        return;
      }
      
      // 跳转到申请页面或显示申请表单
      wx.navigateTo({
        url: `/pages/refund/apply?orderSn=${orderSn}`
      });
      
    } catch (error) {
      wx.showToast({
        title: '检查失败',
        icon: 'error'
      });
    }
  },
  
  // 查看退款详情
  async handleViewDetail(e) {
    const { refundSn } = e.currentTarget.dataset;
    
    wx.navigateTo({
      url: `/pages/refund/detail?refundSn=${refundSn}`
    });
  },
  
  // 取消退款申请
  async handleCancelRefund(e) {
    const { refundId } = e.currentTarget.dataset;
    
    wx.showModal({
      title: '确认取消',
      content: '确定要取消退款申请吗？',
      success: async (res) => {
        if (res.confirm) {
          try {
            await RefundService.cancelRefund(refundId);
            wx.showToast({
              title: '取消成功',
              icon: 'success'
            });
            this.loadRefundList(); // 刷新列表
          } catch (error) {
            wx.showToast({
              title: '取消失败',
              icon: 'error'
            });
          }
        }
      }
    });
  }
});

// 退款申请页面示例
Page({
  data: {
    orderSn: '',
    refundTypes: [],
    refundReasons: [],
    goodsStatuses: [],
    formData: {
      type: 1,
      reason: 1,
      goods_status: 1,
      money: 0,
      content: '',
      image: '',
      name: '',
      mobile: ''
    }
  },
  
  onLoad(options) {
    this.setData({ orderSn: options.orderSn });
    this.loadFormData();
  },
  
  // 加载表单数据
  async loadFormData() {
    try {
      const result = await RefundService.getOptions();
      this.setData({
        refundTypes: Object.entries(result.data.types).map(([value, label]) => ({ value, label })),
        refundReasons: Object.entries(result.data.reasons).map(([value, label]) => ({ value, label })),
        goodsStatuses: Object.entries(result.data.goods_statuses).map(([value, label]) => ({ value, label }))
      });
    } catch (error) {
      wx.showToast({
        title: '加载失败',
        icon: 'error'
      });
    }
  },
  
  // 表单输入处理
  handleInput(e) {
    const { field } = e.currentTarget.dataset;
    const value = e.detail.value;
    
    this.setData({
      [`formData.${field}`]: value
    });
  },
  
  // 选择图片
  async chooseImage() {
    const res = await wx.chooseImage({
      count: 3,
      sizeType: ['compressed'],
      sourceType: ['album', 'camera']
    });
    
    if (res.tempFilePaths.length > 0) {
      // 这里需要上传图片到服务器，获取图片URL
      const imageUrls = res.tempFilePaths.map(path => this.uploadImage(path));
      
      // 等待所有图片上传完成
      const uploadedUrls = await Promise.all(imageUrls);
      this.setData({
        'formData.image': uploadedUrls.join(',')
      });
    }
  },
  
  // 上传图片（需要实现具体的上传逻辑）
  async uploadImage(filePath) {
    // 这里调用文件上传接口
    return 'uploaded-image-url.jpg';
  },
  
  // 提交申请
  async handleSubmit() {
    const { orderSn, formData } = this.data;
    
    // 验证表单
    if (!formData.name || !formData.mobile || !formData.money) {
      wx.showToast({
        title: '请填写完整信息',
        icon: 'error'
      });
      return;
    }
    
    try {
      const result = await RefundService.applyRefund({
        order_sn: orderSn,
        ...formData
      });
      
      wx.showToast({
        title: '申请提交成功',
        icon: 'success'
      });
      
      // 返回上一页
      setTimeout(() => {
        wx.navigateBack();
      }, 1500);
      
    } catch (error) {
      wx.showToast({
        title: error || '提交失败',
        icon: 'error'
      });
    }
  }
});
<extend name="../../Admin/View/Common/element_layout"/>
<block name="content">
    <div id="app" v-cloak>
        <el-card>
            <el-form :model="form" label-width="130px">
                <el-form-item label="公众号名称">
                    <el-input v-model="form.name"></el-input>
                </el-form-item>
                <el-form-item label="公众号类型">
                    <el-radio-group v-model="form.account_type">
                        <el-radio label="office">公众号</el-radio>
                        <el-radio label="mini">小程序</el-radio>
                    </el-radio-group>
                </el-form-item>
                <el-form-item label="app_id">
                    <el-input v-model="form.app_id"></el-input>
                </el-form-item>
                <el-form-item label="secret">
                    <el-input v-model="form.secret"></el-input>
                </el-form-item>
                <el-form-item label="微信支付mch_id">
                    <el-input v-model="form.mch_id"></el-input>
                </el-form-item>
                <el-form-item label="微信支付key">
                    <el-input v-model="form.key"></el-input>
                </el-form-item>
                <el-form-item v-if="form.account_type == 'office'" label="token">
                    <el-input v-model="form.token"></el-input>
                    <div class="el-tip">接受服务推送消息需要配置令牌token(从微信公众号『开发-基本配置』-『服务器配置』中获取)</div>
                </el-form-item>
                <el-form-item v-if="form.account_type == 'office'" label="aes_key">
                    <el-input v-model="form.aes_key"></el-input>
                    <div class="el-tip">消息加解密密钥(从微信公众号『开发-基本配置』-『服务器配置』中获取)</div>
                </el-form-item>
                <el-form-item label="支付cert_path">
                    <el-upload
                            class="upload-demo"
                            action="{:U('Wechat/Wechat/uploadfile')}"
                            :on-success="uploadSuccessCert"
                            :show-file-list="false">

                        <el-button size="small" type="primary">点击上传</el-button>
                        <div slot="tip" class="el-upload__tip">{{form.cert_path}}</div>
                        <div slot="tip" class="el-upload__tip">请上传微信支付的 apiclient_cert.pem文件</div>
                    </el-upload>
                </el-form-item>
                <el-form-item label="支付key_path">
                    <el-upload
                            class="upload-demo"
                            action="{:U('Wechat/Wechat/uploadfile')}"
                            :on-success="uploadSuccessKey"
                            :show-file-list="false">
                        <el-button size="small" type="primary">点击上传</el-button>
                        <div slot="tip" class="el-upload__tip">{{form.key_path}}</div>
                        <div slot="tip" class="el-upload__tip">请上传微信支付的 apiclient_key.pem文件</div>
                    </el-upload>
                </el-form-item>

                <el-form-item label="" style="border-top: 1px solid gainsboro;margin-top: 10px;padding-top: 10px;">
                    <el-button type="primary" @click="submitEvent">确 定</el-button>
                </el-form-item>
            </el-form>

        </el-card>


    </div>
    <script>
        $(document).ready(function () {
            new Vue({
                el: "#app",
                data: {
                    form: {
                        id: '',
                        name: "",
                        account_type: "office",
                        app_id: "",
                        secret: "",
                        mch_id: "",
                        key: "",
                        cert_path: "",
                        key_path: "",
                        token: "",
                        aes_key: "",
                    },
                },
                mounted() {
                    this.form.id = this.getUrlQuery('id')
                    this.getDetail()
                },
                methods: {
                    uploadSuccessKey(res) {
                        if (res.status) {
                            this.form.key_path = this.key_path
                        } else {
                            layer.msg(res.msg)
                        }
                    },
                    uploadSuccessCert(res) {
                        if (res.status) {
                            this.form.cert_path = this.cert_path
                        } else {
                            layer.msg(res.msg)
                        }
                    },
                    getDetail() {
                        var _this = this
                        if (!this.form.id) {
                            return
                        }
                        $.ajax({
                            url: "/Wechat/Wechat/getOfficeDetail?id=" + _this.form.id,
                            dataType: 'json',
                            type: 'get',
                            success: function (res) {
                                console.log("res", res);
                                if (res.status) {
                                    _this.form = res.data;
                                }
                            }
                        })
                    },
                    submitEvent() {
                        var _this = this
                        $.ajax({
                            url: "{:U('Wechat/Wechat/doEditOffice')}",
                            data: _this.form,
                            dataType: 'json',
                            type: 'post',
                            success: function (res) {
                                if (res.status) {
                                    layer.msg('操作成功')
                                    _this.getDetail()
                                } else {
                                    layer.msg(res.msg)
                                }
                            }
                        })
                    }
                }
            })
        });
    </script>
</block>


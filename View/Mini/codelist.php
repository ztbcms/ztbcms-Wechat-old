<extend name="../../Admin/View/Common/element_layout"/>
<block name="content">
    <div id="app" v-cloak>
        <el-card>
            <div slot="header" class="clearfix">
                <span>小程序码列表</span>
            </div>
            <div>
                <el-form :inline="true" :model="searchData" class="demo-form-inline">
                    <el-form-item label="appid">
                        <el-input v-model="searchData.app_id" placeholder="请输入小程序appid"></el-input>
                    </el-form-item>
                    <el-form-item>
                        <el-button type="primary" @click="searchEvent">查询</el-button>
                    </el-form-item>
                    <el-form-item>
                        <el-button type="primary" @click="createCodeEvent">添加小程序码</el-button>
                    </el-form-item>
                </el-form>
            </div>
            <div>
                <el-table
                        :data="users"
                        border
                        style="width: 100%">
                    <el-table-column
                            prop="app_id"
                            align="center"
                            label="appid"
                            min-width="180">
                    </el-table-column>
                    <el-table-column
                            label="小程序码"
                            align="center"
                            min-width="100">
                        <template slot-scope="scope">
                            <img class="avatar" @click="showImageDialogVisible=true;showImageUrl=scope.row.file_url"
                                 :src="scope.row.file_url" alt="">
                        </template>
                    </el-table-column>
                    <el-table-column
                            prop="type"
                            label="类型"
                            align="center"
                            min-width="100">
                    </el-table-column>
                    <el-table-column
                            label="页面路径"
                            align="center"
                            min-width="180">
                        <template slot-scope="scope">
                            <span v-if="scope.row.path">
                                {{scope.row.path}}
                            </span>
                            <span v-else>
                                -
                            </span>
                        </template>
                    </el-table-column>
                    <el-table-column
                            prop="scene"
                            label="参数"
                            align="center"
                            min-width="180">
                    </el-table-column>
                    <el-table-column
                            align="center"
                            label="创建时间"
                            min-width="180">
                        <template slot-scope="scope">
                            {{scope.row.create_time|getFormatDatetime}}
                        </template>
                    </el-table-column>
                    <el-table-column
                            fixed="right"
                            label="操作"
                            align="center"
                            min-width="100">
                        <template slot-scope="scope">
                            <el-button @click="deleteEvent(scope.row)" type="danger">删除</el-button>
                        </template>
                    </el-table-column>
                </el-table>
            </div>
            <div class="page-container">
                <el-pagination
                        background
                        :page-size="limit"
                        :page-count="totalPages"
                        :current-page="page"
                        :total="totalItems"
                        layout="prev, pager, next"
                        @current-change="currentChangeEvent">
                </el-pagination>
            </div>
        </el-card>
        <el-dialog
                :visible.sync="showImageDialogVisible"
                width="300px">
            <div>
                <img style="width: 100%;" :src="showImageUrl" alt="">
            </div>
            <div slot="footer" class="dialog-footer">
                <el-button type="primary" @click="showImageDialogVisible = false">确 定</el-button>
            </div>
        </el-dialog>
        <div>
            <el-dialog
                    title="添加小程序码"
                    :visible.sync="createDialogVisible"
                    width="500px">
                <div>
                    <el-form label-width="120px">
                        <el-form-item label="选择小程序">
                            <el-select v-model="createAppId" placeholder="请选择小程序">
                                <el-option v-for="item in miniOffices" :label="item.name"
                                           :value="item.app_id"></el-option>
                            </el-select>
                        </el-form-item>
                        <el-form-item label="小程序码类型">
                            <el-radio v-model="createCodeType" label="limit">限制类</el-radio>
                            <el-radio v-model="createCodeType" label="unlimit">无限类</el-radio>
                        </el-form-item>
                        <el-form-item label="页面路径">
                            <el-input v-model="createCodePath" placeholder="请输入小程序页面路径 pages/index/main"></el-input>
                            <div class="tip">
                                小程序页面路径，无限类不填默认是首页
                            </div>
                        </el-form-item>
                        <el-form-item label="小程序参数">
                            <el-input v-model="createCodeScene" placeholder="请输入小程序码参数"></el-input>
                            <div v-if="createCodeType=='limit'" class="tip">
                                限制类二维码参数可以是?a=b&b=c
                            </div>
                            <div v-if="createCodeType=='unlimit'" class="tip">
                                无限类二维码参数只可以32位的字符串
                            </div>
                        </el-form-item>
                    </el-form>
                </div>
                <div slot="footer" class="dialog-footer">
                    <el-button @click="createDialogVisible = false">取 消</el-button>
                    <el-button type="primary" @click="submitCreateCode">确 定</el-button>
                </div>
            </el-dialog>
        </div>
    </div>
    <style>
        .avatar {
            width: 60px;
            height: 60px;
            cursor: pointer;
        }

        .page-container {
            margin-top: 0px;
            text-align: center;
            padding: 10px;
        }

        .tip {
            font-size: 12px;
            color: #666666;
        }
    </style>
    <script>
        $(document).ready(function () {
            new Vue({
                el: "#app",
                data: {
                    showImageDialogVisible: false,
                    showImageUrl: "",
                    searchData: {
                        open_id: "",
                        app_id: "",
                        nick_name: ""
                    },
                    users: [],
                    page: 1,
                    limit: 20,
                    totalPages: 0,
                    totalItems: 0,
                    createDialogVisible: false,
                    createAppId: 0,
                    createCodeType: 'limit',
                    createCodePath: '',
                    createCodeScene: '',
                    miniOffices: []

                },
                mounted() {
                    this.getCodeList();
                    this.getMiniOffice();
                },
                methods: {
                    submitCreateCode: function () {
                        const postData = {
                            app_id: this.createAppId,
                            type: this.createCodeType,
                            path: this.createCodePath,
                            scene: this.createCodeScene,
                        };
                        const _this = this;
                        this.httpPost("{:U('Wechat/Mini/createCode')}", postData, function (res) {
                            if (res.status) {
                                _this.$message.success("创建成功");
                                _this.page = 1;
                                _this.getCodeList();
                            } else {
                                _this.$message.error(res.msg);
                            }
                        })
                    },
                    getMiniOffice: function () {
                        const _this = this;
                        //获取小程序
                        this.httpGet('{:U("Wechat/Wechat/index")}', {account_type: "mini"}, function (res) {
                            _this.miniOffices = res.data;
                            if (_this.miniOffices.length > 0) {
                                _this.createAppId = _this.miniOffices[0].app_id;
                            }
                        })
                    },
                    createCodeEvent: function () {
                        this.createDialogVisible = true
                    },
                    deleteEvent: function (row) {
                        var postData = {
                            id: row.id
                        };
                        console.log('callback', postData);
                        var _this = this;
                        this.$confirm('是否确认删除该记录', '提示', {
                            callback: function (e) {
                                if (e !== 'confirm') {
                                    return;
                                }
                                _this.httpPost('{:U("Wechat/Mini/deleteCode")}', postData, function (res) {
                                    if (res.status) {
                                        _this.$message.success('删除成功');
                                        _this.getCodeList();
                                    } else {
                                        _this.$message.error(res.msg);
                                    }
                                })
                            }
                        });

                    },
                    searchEvent: function () {
                        this.page = 1;
                        this.getCodeList();
                    },
                    currentChangeEvent: function (page) {
                        this.page = page;
                        this.getCodeList();
                    },
                    getCodeList: function () {
                        var _this = this;
                        var where = Object.assign({
                            page: this.page,
                            limit: this.limit
                        }, this.searchData);
                        $.ajax({
                            url: "{:U('Wechat/Mini/codeList')}",
                            dataType: 'json',
                            type: 'get',
                            data: where,
                            success: function (res) {
                                console.log("res", res);
                                if (res.status) {
                                    _this.users = res.data.items;
                                    _this.page = res.data.page;
                                    _this.limit = res.data.limit;
                                    _this.totalPages = res.data.total_pages;
                                    _this.totalItems = res.data.total_items
                                }
                            }
                        })
                    }
                }
            })
        });
    </script>
</block>


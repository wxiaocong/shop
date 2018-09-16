<?php
return array(
    'attribute'  => array(
        'type' => array(
            'radio'    => array(
                'code' => 1,
                'text' => '单选框',
            ),
            'checkbox' => array(
                'code' => 2,
                'text' => '复选框',
            ),
            'select'   => array(
                'code' => 3,
                'text' => '下拉框',
            ),
            'input'    => array(
                'code' => 4,
                'text' => '输入框',
            ),
        ),
    ),
    'zeroAndOne' => array(
        'zero' => array(
            'code' => 0,
            'text' => '否',
        ),
        'one'  => array(
            'code' => 1,
            'text' => '是',
        ),
    ),
    'systemLog'  => array(
        'type' => array(
            'add'    => array(
                'code' => 'a',
                'text' => '添加',
            ),
            'update' => array(
                'code' => 'e',
                'text' => '编辑',
            ),
            'delete' => array(
                'code' => 'd',
                'text' => '删除',
            ),
        ),
    ),
    'good'       => array(
        'virtualType' => array(
            'entity'  => array(
                'code' => 1,
                'text' => '实体商品',
            ),
            'virtual' => array(
                'code' => 0,
                'text' => '虚拟商品',
            ),
        ),
        'state'       => array(
            'putaway' => array(
                'code' => 0,
                'text' => '上架',
            ),
            'del'     => array(
                'code' => 1,
                'text' => '删除',
            ),
            'soldOut' => array(
                'code' => 2,
                'text' => '下架',
            ),
        ),
    ),
    'promotion'  => array(
        'type'      => array(
            'shoppingCart' => array(
                'code' => 0,
                'text' => '购物车促销规则',
            ),
            'speed'        => array(
                'code' => 1,
                'text' => '商品限时抢购',
            ),
            'category'     => array(
                'code' => 2,
                'text' => '商品分类特价',
            ),
            'sku'          => array(
                'code' => 3,
                'text' => '商品单品特价',
            ),
            'brand'        => array(
                'code' => 4,
                'text' => '商品品牌特价',
            ),
            'user'         => array(
                'code' => 5,
                'text' => '新用户注册促销规则',
            ),
        ),
        'awardType' => array(
            'speed'        => array(
                'code' => 0,
                'text' => '商品限时抢购',
            ),
            'amount'       => array(
                'code' => 1,
                'text' => '减金额',
            ),
            'discount'     => array(
                'code' => 2,
                'text' => '奖励折扣',
            ),
            'integral'     => array(
                'code' => 3,
                'text' => '赠送积分',
            ),
            'voucher'      => array(
                'code' => 4,
                'text' => '赠送代金券',
            ),
            'gift'         => array(
                'code' => 5,
                'text' => '赠送赠品',
            ),
            'freight'      => array(
                'code' => 6,
                'text' => '免运费',
            ),
            'specialOffer' => array(
                'code' => 7,
                'text' => '商品特价',
            ),
            'suffer'       => array(
                'code' => 8,
                'text' => '赠送经验',
            ),
        ),
    ),
    'order'      => array(
        'state'         => array(
            'waitPay'      => array(
                'code' => 1,
                'text' => '等待付款',
            ),
            'waitDelivery' => array(
                'code' => 2,
                'text' => '已付款准备发货',
            ),
            'waitGood'     => array(
                'code' => 3,
                'text' => '等待收货',
            ),
            'partRefund'   => array(
                'code' => 4,
                'text' => '部分退款',
            ),
            'refund'       => array(
                'code' => 5,
                'text' => '全部退款',
            ),
            'cancel'       => array(
                'code' => 6,
                'text' => '取消',
            ),
            'del'          => array(
                'code' => 7,
                'text' => '删除',
            ),
            'finish'       => array(
                'code' => 8,
                'text' => '完成',
            ),
        ),
        'deliverStatus' => array(
            'waitDelivery' => array(
                'code' => 0,
                'text' => '未发货',
            ),
            'partDelivery' => array(
                'code' => 1,
                'text' => '部分发货',
            ),
            'delivery'     => array(
                'code' => 2,
                'text' => '已发货',
            ),
        ),
        'payType'       => array(
            'wechat'  => array(
                'code' => 1,
                'text' => '微信',
            ),
            'alipay'  => array(
                'code' => 2,
                'text' => '支付宝',
            ),
            'balance' => array(
                'code' => 3,
                'text' => '余额',
            ),
        ),
    ),
    'orderGood'  => array(
        'state' => array(
            'waitDelivery' => array(
                'code' => 1,
                'text' => '未发货',
            ),
            'partDelivery' => array(
                'code' => 2,
                'text' => '部分发货',
            ),
            'delivery'     => array(
                'code' => 3,
                'text' => '全部发货',
            ),
            'partReturn'   => array(
                'code' => 4,
                'text' => '部分退货',
            ),
            'return'       => array(
                'code' => 5,
                'text' => '全部退货',
            ),
            'cancel'       => array(
                'code' => 6,
                'text' => '取消',
            ),
        ),
    ),
    'payLog'     => array(
        'payType' => array(
            'orderPay'      => array(
                'code' => 1,
                'text' => '订单付款',
            ),
            'userDeposit'   => array(
                'code' => 2,
                'text' => '用户充值',
            ),
            'systemDeposit' => array(
                'code' => 3,
                'text' => '后台充值',
            ),
            'refund'        => array(
                'code' => 4,
                'text' => '退款',
            ),
        ),
    ),
    'user'       => array(
        'state'              => array(
            'normal' => array(
                'code' => 1,
                'text' => '正常',
            ),
            'lock'   => array(
                'code' => 2,
                'text' => '锁定',
            ),
        ),
        'businessAuditState' => array(
            'noApply' => array(
                'code' => 0,
                'text' => '未申请',
            ),
            'apply'   => array(
                'code' => 1,
                'text' => '待审核',
            ),
            'pass'    => array(
                'code' => 2,
                'text' => '审核通过',
            ),
            'refuse'  => array(
                'code' => 3,
                'text' => '拒绝',
            ),
        ),
    ),
    'adminRight' => array(
        'showMenu' => array(
            'yes' => array(
                'code' => 1,
                'text' => '是',
            ),
            'no'  => array(
                'code' => 0,
                'text' => '否',
            ),
        ),
    ),
);

<?php
return array(
    'attribute' => array(
        'type' => array(
            'radio' => array(
                'code' => 1,
                'text' => '单选框',
            ),
            'checkbox' => array(
                'code' => 2,
                'text' => '复选框',
            ),
            'select' => array(
                'code' => 3,
                'text' => '下拉框',
            ),
            'input' => array(
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
        'one' => array(
            'code' => 1,
            'text' => '是',
        ),
    ),
    'systemLog' => array(
        'type' => array(
            'add' => array(
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
    'good' => array(
        'state' => array(
            'putaway' => array(
                'code' => 0,
                'text' => '上架',
            ),
            'del' => array(
                'code' => 1,
                'text' => '删除',
            ),
            'soldOut' => array(
                'code' => 2,
                'text' => '下架',
            ),
        ),
    ),
    'order' => array(
        'state' => array(
            'waitPay' => array(
                'code' => 1,
                'text' => '等待付款',
            ),
            'waitDelivery' => array(
                'code' => 2,
                'text' => '已付款准备发货',
            ),
            'waitGood' => array(
                'code' => 3,
                'text' => '等待收货',
            ),
            'partRefund' => array(
                'code' => 4,
                'text' => '部分退款',
            ),
            'refund' => array(
                'code' => 5,
                'text' => '全部退款',
            ),
            'cancel' => array(
                'code' => 6,
                'text' => '取消',
            ),
            'del' => array(
                'code' => 7,
                'text' => '删除',
            ),
            'finish' => array(
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
            'delivery' => array(
                'code' => 2,
                'text' => '已发货',
            ),
        ),
        'payType' => array(
            'wechat' => array(
                'code' => 1,
                'text' => '微信',
            ),
            'alipay' => array(
                'code' => 2,
                'text' => '支付宝',
            ),
            'balance' => array(
                'code' => 3,
                'text' => '余额',
            ),
        ),
    ),
    'orderGood' => array(
        'state' => array(
            'waitDelivery' => array(
                'code' => 1,
                'text' => '未发货',
            ),
            'partDelivery' => array(
                'code' => 2,
                'text' => '部分发货',
            ),
            'delivery' => array(
                'code' => 3,
                'text' => '全部发货',
            ),
            'partReturn' => array(
                'code' => 4,
                'text' => '部分退货',
            ),
            'return' => array(
                'code' => 5,
                'text' => '全部退货',
            ),
            'cancel' => array(
                'code' => 6,
                'text' => '取消',
            ),
        ),
    ),
        //'1订单付款,2用户充值,3后台充值,4退款,5下级销售提成,6下下级销售提成,7VIP奖励'
    'payLog' => array(
        'payType' => array(
            'orderPay' => array(
                'code' => 1,
                'text' => '订单付款',
            ),
            'userDeposit' => array(
                'code' => 2,
                'text' => '用户充值',
            ),
            'systemDeposit' => array(
                'code' => 3,
                'text' => '后台充值',
            ),
            'refund' => array(
                'code' => 4,
                'text' => '退款',
            ),
            'subordinate' => array(
                'code' => 5,
                'text' => '销售提成'
            ),
            'lowest' => array(
                'code' => 6,
                'text' => '协助收益'
            ),
            'vip' => array(
                'code' => 7,
                'text' => 'vip额外奖励'
            )
        ),
        'pageType' => array(
             'fund' => array(5,6,7)
        )
    ),
    'user' => array(
        'state' => array(
            'normal' => array(
                'code' => 1,
                'text' => '正常',
            ),
            'lock' => array(
                'code' => 2,
                'text' => '锁定',
            ),
        ),
        'levelState' => array(
            '0' => '游客',
            '1' => '艾达人',
            '2' => '艾天使',
        ),
        'levelType' => array(
            '0' => '全部',
            '1' => '游客',
            '2' => '艾达人',
            '3' => '艾天使'
        )
    ),
    'adminRight' => array(
        'showMenu' => array(
            'yes' => array(
                'code' => 1,
                'text' => '是',
            ),
            'no' => array(
                'code' => 0,
                'text' => '否',
            ),
        ),
    ),
);

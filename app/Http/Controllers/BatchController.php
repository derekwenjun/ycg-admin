<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Batch, App\Order, App\Tracking, App\BatchTracking;

use Auth;
use Log;

class BatchController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index(Request $request)
    {
        $batches = Batch::orderBy('id', 'desc')->withCount('orders');
        if ($request->has('id')) $batches = $batches->where('id', $request->id);

        // request flash to access the old value
        $request->flash();

        $batches = $batches->paginate(20);
        $batches->appends($request->all());

        return view('batches.index', ['nav' => 'batch', 'batches' => $batches ]);
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function show(Request $request, $id)
    {
        $batch = Batch::withCount('orders')->find($id);
        return view('batches.show', ['nav' => 'batch', 'batch' => $batch]);
    }
    
    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function store(Request $request)
    {
        $batch = new Batch;
        $batch->remark = $request->remark;
        $batch->status_id = 0;
        $batch->save();
        
        // 生成批次创建追踪信息
        $tracking = new BatchTracking;
        $tracking->batch_id = $batch->id;
        $tracking->description = '批次已创建';
        $tracking->save();
        
        return redirect()->route('batch.index');
    }
    
    /**
     * 添加一个包裹进入批次
     *
     * @param  int  $id
     * @return Response
     */
    public function add(Request $request, $id)
    {
        if ($request->has('no')) {
            // 根据运单号查询包裹
            $order = Order::where('no', $request->no)->first();
            
            // 1. 如果包裹不存在
            if(is_null($order)) {
                $request->session()->flash('error', '包裹 - ' . $request->no . ' 不存在');
                return redirect()->route('batch.show', ['id' => $id]);
            }
            
            // 2. 该包裹已经在批次中
            if($order->batch_id == $id) {
                $request->session()->flash('error', '包裹 - ' . $request->no . ' 已经在该批次中，无需重复添加');
                return redirect()->route('batch.show', ['id' => $id]);
            }
            
            // 3. 该包裹在其他批次中
            if($order->batch_id != 0) {
                $request->session()->flash('error', '包裹 - ' . $request->no . ' 已经在其他批次中，无法加入该批次');
                return redirect()->route('batch.show', ['id' => $id]);
            }
            
            // 4. 该包裹目前不在总仓中
            if($order->status_id != 3) {
                $request->session()->flash('error', '包裹 - ' . $request->no . ' 当前不在总仓中');
                return redirect()->route('batch.show', ['id' => $id]);
            }
            
            // 添加包裹进入批次中
            $order->batch_id = $id;
            $order->save();
            $request->session()->flash('status', '包裹 - ' . $order->no . ' 成功添加进批次中！');
        }
        return redirect()->route('batch.show', ['id' => $id]);
    }
    
    /**
     * 从批次中删除一个包裹
     *
     * @param  int  $id
     * @return Response
     */
    public function remove(Request $request, $id)
    {
        // 查找包裹并且删除
        $order = Order::find($id);
        $order->batch_id = 0;
        $order->save();
        
        $request->session()->flash('status', '包裹 - ' . $order->no . ' 成功从批次中删除！');
        return redirect()->route('batch.show', ['id' => $request->bid]);
    }
    
    /**
     * 0 ===> 1  从集运仓出库
     * 
     * @param  int  $id
     * @return Response
     */
    public function to1(Request $request, $id)
    {
        // 修改批次本身状态
        $batch = Batch::find($id);
        // 如果状态不对，则直接返回
        if($batch->status_id >= 1) {
            return redirect()->route('batch.show', ['id' => $id]);
        }
        $batch->status_id = 1;
        $batch->save();
        
        // 生成批次出库追踪信息
        $tracking = new BatchTracking;
        $tracking->batch_id = $batch->id;
        $tracking->description = '批次已出库';
        $tracking->save();
        
        // 为批次下的所有订单添加追踪信息，并且修改状态
        foreach($batch->orders as $order) {
            $order->status_id = 4;
            $order->save();
            
            // 生成出库追踪信息
            $tracking = new Tracking;
            $tracking->order_id = $order->id;
            $tracking->location = '西班牙';
            $tracking->description = '包裹已离开集运仓 ' . $request->tip;
            $tracking->save();
        }
        
        $request->session()->flash('status', '批次已经成功从集运仓发出！');
        return redirect()->route('batch.show', ['id' => $id]);
    }
    
    /**
     * 1 ===> 5  批次航班从机场起飞
     *
     * @param  int  $id
     * @return Response
     */
    public function to5(Request $request, $id)
    {
        // 修改批次本身状态
        $batch = Batch::find($id);
        // 如果状态不对，则直接返回
        if($batch->status_id >= 5) {
            return redirect()->route('batch.show', ['id' => $id]);
        }
        $batch->status_id = 5;
        $batch->save();
        
        // 生成批次出库追踪信息
        $tracking = new BatchTracking;
        $tracking->batch_id = $batch->id;
        $tracking->description = '批次航班已起飞';
        $tracking->save();
        
        // 为批次下的所有订单添加追踪信息，并且修改状态
        foreach($batch->orders as $order) {
            // 生成出库追踪信息
            $tracking = new Tracking;
            $tracking->order_id = $order->id;
            $tracking->location = '西班牙';
            $tracking->description = '干线航班已从机场起飞 航班号:' . $request->tip;
            $tracking->save();
        }
        
        $request->session()->flash('status', '批次航班已经起飞！');
        return redirect()->route('batch.show', ['id' => $id]);
    }
    
    /**
     * 5 ===> 10  批次航班到达国内某机场
     *
     * @param  int  $id
     * @return Response
     */
    public function to10(Request $request, $id)
    {
        // 修改批次本身状态
        $batch = Batch::find($id);
        // 如果状态不对，则直接返回
        if($batch->status_id >= 10) {
            return redirect()->route('batch.show', ['id' => $id]);
        }
        $batch->status_id = 10;
        $batch->save();
        
        // 生成批次出库追踪信息
        $tracking = new BatchTracking;
        $tracking->batch_id = $batch->id;
        $tracking->description = '批次航班已到达';
        $tracking->save();
        
        // 为批次下的所有订单添加追踪信息，并且修改状态
        foreach($batch->orders as $order) {
            // 生成出库追踪信息
            $tracking = new Tracking;
            $tracking->order_id = $order->id;
            $tracking->location = $request->city;
            $tracking->description = '干线航班已到达 ' . $request->airport;
            $tracking->save();
        }
        
        $request->session()->flash('status', '批次航班已经到达' . $request->airport);
        return redirect()->route('batch.show', ['id' => $id]);
    }
    
    /**
     * 10 ===> 15  批次航班到达国内某机场
     *
     * @param  int  $id
     * @return Response
     */
    public function to15(Request $request, $id)
    {
        // 修改批次本身状态
        $batch = Batch::find($id);
        // 如果状态不对，则直接返回
        if($batch->status_id >= 15) {
            return redirect()->route('batch.show', ['id' => $id]);
        }
        $batch->status_id = 15;
        $batch->save();
        
        // 生成批次出库追踪信息
        $tracking = new BatchTracking;
        $tracking->batch_id = $batch->id;
        $tracking->description = '批次已从机场提货';
        $tracking->save();
        
        // 为批次下的所有订单添加追踪信息，并且修改状态
        foreach($batch->orders as $order) {
            // 生成出库追踪信息
            $tracking = new Tracking;
            $tracking->order_id = $order->id;
            $tracking->location = $request->city;
            $tracking->description = '已从机场提货';
            $tracking->save();
        }
        
        $request->session()->flash('status', '批次已从机场提货');
        return redirect()->route('batch.show', ['id' => $id]);
    }
    
    /**
     * 15 ===> 20  批次正在某口岸清关
     *
     * @param  int  $id
     * @return Response
     */
    public function to20(Request $request, $id)
    {
        // 修改批次本身状态
        $batch = Batch::find($id);
        // 如果状态不对，则直接返回
        if($batch->status_id >= 20) {
            return redirect()->route('batch.show', ['id' => $id]);
        }
        $batch->status_id = 20;
        $batch->save();
        
        // 生成批次出库追踪信息
        $tracking = new BatchTracking;
        $tracking->batch_id = $batch->id;
        $tracking->description = '批次正在清关';
        $tracking->save();
        
        // 为批次下的所有订单添加追踪信息，并且修改状态
        foreach($batch->orders as $order) {
            // 生成出库追踪信息
            $tracking = new Tracking;
            $tracking->order_id = $order->id;
            $tracking->location = $request->city;
            $tracking->description = $request->port . ' 已在清关';
            $tracking->save();
        }
        
        $request->session()->flash('status', '批次正在清关');
        return redirect()->route('batch.show', ['id' => $id]);
    }
    
    /**
     * 20 ===> 25  批次已经转交落地配
     *
     * @param  int  $id
     * @return Response
     */
    public function to25(Request $request, $id)
    {
        // 修改批次本身状态
        $batch = Batch::find($id);
        // 如果状态不对，则直接返回
        if($batch->status_id >= 25) {
            return redirect()->route('batch.show', ['id' => $id]);
        }
        $batch->status_id = 25;
        $batch->save();
        
        // 生成批次出库追踪信息
        $tracking = new BatchTracking;
        $tracking->batch_id = $batch->id;
        $tracking->description = '批次已转交落地配';
        $tracking->save();
        
        // 为批次下的所有订单添加追踪信息，并且修改状态
        foreach($batch->orders as $order) {
            // 更改订单状态
            $order->status_id = 5;
            $order->save();
            
            // 生成出库追踪信息
            $tracking = new Tracking;
            $tracking->order_id = $order->id;
            $tracking->location = $request->city;
            $tracking->description = '已完成清关';
            $tracking->save();
            
            // 生成出库追踪信息
            $tracking = new Tracking;
            $tracking->order_id = $order->id;
            $tracking->location = $request->city;
            $tracking->description = '当地快递公司已经揽件';
            $tracking->save();
        }
        
        $request->session()->flash('status', '批次已转交落地配');
        return redirect()->route('batch.show', ['id' => $id]);
    }
    
}

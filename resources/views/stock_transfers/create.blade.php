{{-- resources/views/stock_transfers/create.blade.php --}}
@extends('layouts.adminbase')

@section('content')
<div class="container">
  <h3>โอนสินค้า (Branch Stock Transfer)</h3>
  @if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
  @endif

  <form action="{{ route('stock_transfers.store') }}" method="POST">
    @csrf
    <div class="row mb-3">
      <div class="col">
        <label>จากสาขา</label>
        <select name="from_branch" class="form-select">
          <option value="">-- เลือก --</option>
          @foreach($branches as $b)
            <option value="{{ $b->branch_id }}">{{ $b->branch_name }}</option>
          @endforeach
        </select>
      </div>
      <div class="col">
        <label>ไปยังสาขา</label>
        <select name="to_branch" class="form-select">
          <option value="">-- เลือก --</option>
          @foreach($branches as $b)
            <option value="{{ $b->branch_id }}">{{ $b->branch_name }}</option>
          @endforeach
        </select>
      </div>
      <div class="col">
        <label>วันที่โอน</label>
        <input type="date" name="transfer_date" class="form-control" value="{{ date('Y-m-d') }}">
      </div>
    </div>

    <h5>รายการโอน</h5>
    <table class="table" id="transferTable">
      <thead>
        <tr>
          <th>#</th><th>สินค้า</th><th>คงเหลือ</th><th>จำนวน</th>
        </tr>
      </thead>
      <tbody>
        <tr class="transfer-row">
          <td class="row-index">1</td>
          <td>
            <select name="item_id[]" class="form-select select-item">
              <option value="">-- เลือก --</option>
              @foreach($purchaseItems as $pi)
                @php $rem = $pi->item_quantity - ($pi->used_quantity ?? 0); @endphp
                <option value="{{ $pi->id }}" data-remain="{{ $rem }}">
                  {{ $pi->item_name }} ({{ $rem }} คงเหลือ)
                </option>
              @endforeach
            </select>
          </td>
          <td><span class="remain-qty">0</span></td>
          <td><input name="qty[]" type="number" class="form-control" value="0" min="1"></td>
        </tr>
      </tbody>
    </table>
    <button type="button" id="addRowBtn" class="btn btn-secondary">+ เพิ่มรายการ</button>
    <button type="submit" class="btn btn-primary mt-3">บันทึกโอน</button>
  </form>
</div>
@endsection

@section('js')
<script>
document.addEventListener('DOMContentLoaded',function(){
  const tbody=document.querySelector('#transferTable tbody');
  document.getElementById('addRowBtn').onclick=function(){
    const clone=tbody.querySelector('tr').cloneNode(true);
    clone.querySelectorAll('select, input').forEach(el=>el.value=el.tagName=='INPUT'?0:'');
    tbody.appendChild(clone);
    updateIndices();
  };
  tbody.onclick=function(e){
    if(e.target.matches('.transfer-row .remove-row')){
      if(tbody.children.length>1) { e.target.closest('tr').remove(); updateIndices(); }
    }
  };
  tbody.onchange=function(e){
    if(e.target.matches('.select-item')){
      const tr=e.target.closest('tr');
      const rem=e.target.selectedOptions[0].dataset.remain||0;
      tr.querySelector('.remain-qty').textContent=rem;
      const qtyInput=tr.querySelector('input[name="qty[]"]');
      qtyInput.max=rem;
      if(qtyInput.value>rem) qtyInput.value=rem;
    }
  };
  tbody.oninput=function(e){
    if(e.target.matches('input[name="qty[]"]')){
      const tr=e.target.closest('tr');
      const mx=tr.querySelector('.select-item').selectedOptions[0].dataset.remain||0;
      let v=parseInt(e.target.value)||0;
      if(v>mx)v=mx; if(v<1)v=1; e.target.value=v;
    }
  };
  function updateIndices(){
    tbody.querySelectorAll('tr').forEach((tr,i)=>{ tr.querySelector('.row-index').textContent=i+1; });
  }
  updateIndices();
});
</script>
@endsection

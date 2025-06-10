 <!-- ======= Sidebar ======= -->
 <aside id="sidebar" class="sidebar">

     <ul class="sidebar-nav" id="sidebar-nav">

         

         <li class="nav-item">
             <a class="nav-link collapsed" data-bs-target="#components-nav" data-bs-toggle="collapse" href="#">
                 <i class="bi bi-menu-button-wide"></i><span>ตั้งค่า</span><i class="bi bi-chevron-down ms-auto"></i>
             </a>
             <ul id="components-nav" class="nav-content collapse " data-bs-parent="#sidebar-nav">
                 <li>
                     <a href="{{ route('company.index') }}">
                         <i class="bi bi-circle"></i><span>บริษัท</span>
                     </a>
                 </li>
                 <li>
                     <a href="{{ route('branches.index') }}">
                         <i class="bi bi-circle"></i><span>สาขา</span>
                     </a>
                 </li>
                 <li>
                     <a href="{{ route('partners.index') }}">
                         <i class="bi bi-circle"></i><span>ผู้จำหน่าย/ผู้ขาย</span>
                     </a>
                 </li>
                 <li>
                    <a href="{{ route('sub_expenss.index') }}">
                        <i class="bi bi-circle"></i><span>ค่าใช้จ่ายอื่นๆ</span>
                    </a>
                </li>
                 
             </ul>
         </li><!-- End Components Nav -->

         <li class="nav-item">
             <a class="nav-link collapsed" data-bs-target="#forms-nav" data-bs-toggle="collapse" href="#">
                 <i class="bi bi-journal-text"></i><span>รับเข้า</span><i class="bi bi-chevron-down ms-auto"></i>
             </a>
             <ul id="forms-nav" class="nav-content collapse " data-bs-parent="#sidebar-nav">
                 <li>
                     <a href="{{ route('purchases.index') }}">
                         <i class="bi bi-circle"></i><span>รับซื้อบริษัท - บุคคล</span>
                     </a>
                 </li>
                 <li>
                     <a href="#">
                         <i class="bi bi-circle"></i><span>บันทึกค่าใช้จ่าย</span>
                     </a>
                 </li>
                 
             </ul>
         </li><!-- End Forms Nav -->

         <li class="nav-item">
             <a class="nav-link collapsed" data-bs-target="#tables-nav" data-bs-toggle="collapse" href="#">
                 <i class="bi bi-layout-text-window-reverse"></i><span>คลัง</span><i
                     class="bi bi-chevron-down ms-auto"></i>
             </a>
             <ul id="tables-nav" class="nav-content collapse " data-bs-parent="#sidebar-nav">
                 <li>
                     <a href="{{ route('purchaseitems.index') }}">
                         <i class="bi bi-circle"></i><span>รายการสินค้า</span>
                     </a>
                 </li>
                 <li>
                     <a href="{{ route('stocks.index') }}">
                         <i class="bi bi-circle"></i><span>สต๊อก</span>
                     </a>
                 </li>               
                 <li>
                     <a href="#">
                         <i class="bi bi-circle"></i><span>พิมพ์บาร์</span>
                     </a>
                 </li>
                 <li>
                    <a href="#">
                        <i class="bi bi-circle"></i><span>โอน</span>
                    </a>
                </li>
                <li>
                    <a href="#">
                        <i class="bi bi-circle"></i><span>รับ</span>
                    </a>
                </li>
                <li>
                    <a href="#">
                        <i class="bi bi-circle"></i><span>รายงาน</span>
                    </a>
                </li>
                <li>
                    <a href="#">
                        <i class="bi bi-circle"></i><span>ยกเลิกการโอน</span>
                    </a>
                </li>
                <li>
                    <a href="#">
                        <i class="bi bi-circle"></i><span>นับสต๊อก</span>
                    </a>
                </li>
             </ul>
         </li><!-- End Tables Nav -->

        

     </ul>

 </aside><!-- End Sidebar-->

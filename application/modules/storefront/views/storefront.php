<?php echo $this->load->view('template/navmain.php'); ?>
<style>
    .main_store{
        margin-top: 60px;
        padding: 20px 30px;
        transition: all 0.3s;
    }
    .detail{
        padding: 20px 0 0 0;
        font-size: 14px;
        font-weight: 500;
        color: #012970;
        font-family: "Poppins", sans-serif;
    }
</style>
<main id="" class="main main_store">

	<div class="pagetitle">
		<h1>หน้าร้าน</h1>
		<nav>
			<ol class="breadcrumb">
				<li class="breadcrumb-item"><a href="#">สินค้าทั้งหมด</a></li>
				<!-- <li class="breadcrumb-item active">Dashboard</li> -->
			</ol>
		</nav>
	</div><!-- End Page Title -->

    <div class="row" style='margin-bottom: 25px; justify-content: space-between;'>
        <div class="col-lg-4 col-sm-8">
            <select class="form-select" id="type_product" name="type_product" aria-label="Estatus_type">
                <option value="0">--สินค้าทั้งหมด--</option>
                <?php for ($i = 0; $i < count($rec_type); $i++) {?>
                <option value="<?=$rec_type[$i]['code_type']?>"><?=$rec_type[$i]['name_type']?></option>
                <?php }?>
            </select>
        </div>

        <div class="col-lg-2 col-sm-4">
            <select class="form-select" id="orderby" name="orderby" aria-label="Estatus_type">
                <option value="0">เรียงลำดับข้อมูล</option>
                <option value="1">ราคาต่ำ - สูง</option>
                <option value="2">ราคาสูง - ต่ำ</option>
            </select>
        </div>
    </div>

	<section class="section dashboard">
		<div class="row">

			<div class="col-lg-12">
				<div class="row">

                <?php for ($i = 0; $i < 50; $i++) {?>

                    <div class="col-xxl-3 col-md-6">

                        <div class="card">
                            <img src="https://res-5.cloudinary.com/central-pet-n-me/image/upload/c_lpad,dpr_2.0,f_auto,q_auto/v1/media/catalog/product/2/1/2101011130002-6_1.jpg?_i=AB" class="card-img-top" alt="...">
                            <div class="card-body">
                                <h5 class="card-title">Card with an image on top</h5>
                                <p class="card-text">Some quick example text to build on the card title and make up the bulk of the card's content.</p>
                                <span class="text-muted small pt-2 ps-1">ราคา/ชิ้น </span><span class="text-secondary small pt-1 fw-bold">300 บาท</span><br>
                                <span class="text-muted small pt-2 ps-1">สินค้าที่มี </span><span class="text-success small pt-1 fw-bold">300 ชิ้น</span><br>
                                <a style='margin-top: 15px;' class="btn btn-primary btn-sm" onclick="add_cart()">หยิบใส่ตะกร้า</a>
                            </div>
                        </div>

					</div>

                <?php }?>

				</div>
			</div>

		</div>
	</section>

</main>

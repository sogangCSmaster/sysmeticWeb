                            <div class="title_a">
								<strong class="title">댓글 <span class="cnt" id="total_cnt" data-cnt="<?=$count?>"><?=number_format($count)?></span>개</strong>
							</div>
							<div class="write_a">
                                <form id="commentFrm" method="post">
								<div class="row">
									<div class="textarea">
										<textarea id="contents" name="contents" placeholder="댓글을 입력해주세요."></textarea>
									</div>
									<button type="submit" class="btn_write">등록</button>
								</div>
								<div class="secret">
									<div class="choice">
										<input type="checkbox" id="secret" name="secret" value="1" />
										<label for="secret">비공개</label>
									</div>
									<p class="info">비공개 댓글은 게시글 작성자와 댓글 작성자만 볼 수 있습니다.</p>
								</div>
                                </form>
							</div>
							<div class="reply_list" id="reply_list">
								<ul>

								</ul>
							</div>
.
							<a href="javascript:;" class="btn_list_more" style="display:none">+ 더보기</a>


                            <script>
                            $(function() {

                                $(".reply_area #commentFrm").submit(function(){
                                    <? if (!$isLoggedIn()) { ?>
                                    if (confirm("로그인이 필요합니다\n로그인 하시겠습니까?")) {
                                        login();
                                        return false;
                                    } else {
                                        return false;
                                    }
                                    <? } ?>

                                    if (!$('#contents').val()) {
                                        $('#contents').focus();
                                        alert('내용을 입력해주세요');
                                        return false;
                                    }

                                    $.post("/pb/bbs/<?=$bid?>/reply/write", $(this).serialize(), function(data){
                                        if (data.result) {
                                            $('.reply_area').load("/pb/bbs/<?=$bid?>/reply", function() {
                                                page = 1;
                                                getComment(page);
                                            });
                                        } else {
                                            alert('처리 중 요류가 발생하였습니다');
                                        }
                                    }, 'json');
                                    return false;
                                });

                            });

                            function modifyFrm(f) {
                                if (f.contents.value == "") {
                                    alert('내용을 입력해주세요');
                                    return false;
                                } else {
                                    if (confirm('수정하시겠습니까?')) {
                                        $.post("/pb/bbs/<?=$bid?>/reply/modify", $(f).serialize(), function(data){
                                            if (data.result) {
                                                $('.reply_area').load("/pb/bbs/<?=$bid?>/reply", function() {
                                                    page = 1;
                                                    getComment(page);
                                                });
                                            } else {
                                                alert('처리 중 요류가 발생하였습니다');
                                            }
                                        }, 'json');
                                        return false;
                                    } else {
                                        return false;
                                    }
                                }
                            }

                            function deleteFrm(cid) {
                                if (confirm('삭제하시겠습니까?')) {
                                    $.post("/pb/bbs/<?=$bid?>/reply/delete", {cid: cid}, function(data){
                                        if (data.result) {
                                            $('.reply_area').load("/pb/bbs/<?=$bid?>/reply", function() {
                                                page = 1;
                                                getComment(page);
                                            });
                                        } else {
                                            alert('처리 중 요류가 발생하였습니다');
                                        }
                                    }, 'json');
                                    return false;
                                } else {
                                    return false;
                                }
                            }
                            </script>
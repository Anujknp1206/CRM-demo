import { useSelector, useDispatch } from "react-redux";
import { useNavigate } from "react-router-dom";
import { setFilters } from "../features/slices/blogSlice";

export const useCategories = () => {
  const dispatch = useDispatch();
  const navigate = useNavigate();
  const { categories } = useSelector((state) => state.categories);

  const handleClick = (categoryId) => {
    dispatch(setFilters({ categories: [categoryId], tags: [] }));
    navigate(`/blogs?categories=${categoryId}`);
  };

  return [ categories, handleClick ];
};

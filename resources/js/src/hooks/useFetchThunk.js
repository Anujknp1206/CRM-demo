import { useEffect } from "react";
import { useDispatch } from "react-redux";

export function useFetchThunk(fetchConfigs = [], deps = []) {
  const dispatch = useDispatch();

  useEffect(() => {
    if (!Array.isArray(fetchConfigs)) return;

    fetchConfigs.forEach(({ thunk, params }) => {
      if (typeof thunk === "function") {
        dispatch(params !== undefined ? thunk(params) : thunk());
      }
    });
  }, [dispatch, fetchConfigs, ...deps]);
}

// const fetchPageConfigs = [{ thunk: fetchPageContent }];
//   fetchPageConfigs[0].params = filteredItems[0].id;
// useFetchThunk(fetchPageConfigs);